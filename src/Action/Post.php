<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Action;

use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\CanControlTransaction;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class Post
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(EventStore $eventStore, MessageFactory $messageFactory)
    {
        $this->eventStore = $eventStore;
        $this->messageFactory = $messageFactory;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        if ($request->getHeaderLine('Content-Type') !== 'application/vnd.eventstore.atom+json') {
            return new JsonResponse('', 415);
        }

        $readEvents = $request->getParsedBody();
        $events = [];

        foreach ($readEvents as $event) {
            if (! is_array($event)
                || ! isset($event['message_name'])
            ) {
                return $response->withStatus(400);
            }

            try {
                $events[] = $this->messageFactory->createMessageFromArray($event['message_name'], $event);
            } catch (\Throwable $e) {
                return $response->withStatus(400, $e->getMessage());
            }
        }

        $streamName = new StreamName(urldecode($request->getAttribute('streamname')));

        $appendToStream = $this->eventStore->hasStream($streamName);

        if ($this->eventStore instanceof CanControlTransaction) {
            $this->eventStore->beginTransaction();
        }

        try {
            if ($appendToStream) {
                $this->eventStore->appendTo($streamName, new \ArrayIterator($events));
            } else {
                $this->eventStore->create(new Stream($streamName, new \ArrayIterator($events)));
            }
        } catch (\Throwable $e) {
            if ($this->eventStore instanceof CanControlTransaction) {
                $this->eventStore->rollback();
            }

            return $response->withStatus(500, 'Cannot create or append to stream');
        }

        if ($this->eventStore instanceof CanControlTransaction) {
            $this->eventStore->commit();
        }

        return $response->withStatus(201, 'Created');
    }
}
