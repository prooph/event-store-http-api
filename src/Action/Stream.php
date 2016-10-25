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

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\StreamName;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

final class Stream
{
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $streamName = $request->getAttribute('streamname');

        $accepted = [
            'application/vnd.eventstore.atom+json',
            'application/atom+json',
            'application/json'
        ];

        if (in_array($request->getHeaderLine('Accept'), $accepted, true)) {
            return $this->returnDescription($streamName);
        }

        $stream = $this->eventStore->load(new StreamName($streamName));

        if (! $stream || ! $stream->streamEvents()->valid()) {
            return new HtmlResponse('', 404);
        }

        echo '<pre>';
        foreach ($stream->streamEvents() as $event) {
            echo $event->messageName() . PHP_EOL;
            echo $event->version() . PHP_EOL;
        }
    }

    private function returnDescription(string $streamName): JsonResponse
    {
        return new JsonResponse(
            [
                'title' => 'Description document for \'' . $streamName . '\'',
                'description' => 'The description document will be presented when no accept header is present or it was requested',
                '_links' => [
                    'self' => [
                        'href' => '/streams/' . $streamName,
                        'supportedContentTypes' => [
                            'application/vnd.eventstore.streamdesc+json'
                        ],
                    ],
                    'stream' => [
                        'href' => '/streams/' . $streamName,
                        'supportedContentTypes' => [
                            'application/vnd.eventstore.atom+json'
                        ],
                    ],
                ],
            ],
            200,
            [
                'Content-Type' => 'application/vnd.eventstore.streamdesc+json; charset=utf-8',
            ]
        );
    }
}
