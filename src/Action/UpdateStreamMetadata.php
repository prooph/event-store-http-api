<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Exception\StreamNotFound;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

final class UpdateStreamMetadata implements MiddlewareInterface
{
    /**
     * @var EventStore
     */
    private $eventStore;

    private $validRequestContentTypes = [
        'application/vnd.eventstore.atom+json',
        'application/json',
        'application/atom+json',
    ];

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $streamName = urldecode($request->getAttribute('streamname'));

        if (! in_array($request->getHeaderLine('Content-Type'), $this->validRequestContentTypes)) {
            return new EmptyResponse(415);
        }

        $metadata = $request->getParsedBody();

        try {
            $this->eventStore->updateStreamMetadata(new StreamName($streamName), $metadata);
        } catch (StreamNotFound $e) {
            return new EmptyResponse(404);
        }

        return new EmptyResponse(204);
    }
}
