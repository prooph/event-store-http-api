<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Action;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeZone;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Prooph\EventStore\TransactionalEventStore;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zend\Diactoros\Response\JsonResponse;

class Post implements MiddlewareInterface
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

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if ($request->getHeaderLine('Content-Type') !== 'application/vnd.eventstore.atom+json') {
            return new JsonResponse('', 415);
        }

        $readEvents = $request->getParsedBody();
        $events = [];

        foreach ($readEvents as $event) {
            if (! is_array($event) || ! isset($event['message_name'])) {
                return new JsonResponse('', 500);
            }

            if (! isset($event['created_at'])) {
                $event['created_at'] = new DateTimeImmutable('now', new DateTimeZone('UTC'));
            } else {
                $event['created_at'] = DateTimeImmutable::createFromFormat(
                    'Y-m-d\TH:i:s.u',
                    $event['created_at'],
                    new DateTimeZone('UTC')
                );
            }

            if (! $event['created_at'] instanceof DateTimeImmutable) {
                $response = new JsonResponse('');
                return $response->withStatus(400, 'Invalid created_at format, expected Y-m-d\TH:i:s.u');
            }

            try {
                $events[] = $this->messageFactory->createMessageFromArray($event['message_name'], $event);
            } catch (Throwable $e) {
                $response = new JsonResponse('');
                return $response->withStatus(400, $e->getMessage());
            }
        }

        $streamName = new StreamName(urldecode($request->getAttribute('streamname')));

        if ($this->eventStore instanceof TransactionalEventStore) {
            $this->eventStore->beginTransaction();
        }

        try {
            if ($this->eventStore->hasStream($streamName)) {
                $this->eventStore->appendTo($streamName, new ArrayIterator($events));
            } else {
                $this->eventStore->create(new Stream($streamName, new ArrayIterator($events)));
            }
        } catch (Throwable $e) {
            if ($this->eventStore instanceof TransactionalEventStore) {
                $this->eventStore->rollback();
            }

            $response = new JsonResponse('');
            return $response->withStatus(500, 'Cannot create or append to stream');
        }

        if ($this->eventStore instanceof TransactionalEventStore) {
            $this->eventStore->commit();
        }

        return new JsonResponse('', 201);
    }
}
