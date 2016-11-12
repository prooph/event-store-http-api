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

use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class Load
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var MessageConverter
     */
    private $messageConverter;

    public function __construct(EventStore $eventStore, MessageConverter $messageConverter)
    {
        $this->eventStore = $eventStore;
        $this->messageConverter = $messageConverter;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $streamName = urldecode($request->getAttribute('streamname'));

        $accepted = [
            'application/vnd.eventstore.atom+json',
            'application/atom+json',
            'application/json'
        ];

        if (! in_array($request->getHeaderLine('Accept'), $accepted, true)) {
            return $this->returnDescription($streamName);
        }

        $start = $request->getAttribute('start');
        if ('head' === $start) {
            $start = PHP_INT_MAX;
        }
        $start = (int) $start;

        $count = (int) $request->getAttribute('count');

        $direction = $request->getAttribute('direction');

        if (PHP_INT_MAX === $start && 'forward' === $direction) {
            return new JsonResponse('', 400);
        }

        if ($direction === 'backward') {
            $stream = $this->eventStore->loadReverse(new StreamName($streamName), $start, $count);
        } else {
            $stream = $this->eventStore->load(new StreamName($streamName), $start, $count);
        }

        if (! $stream || ! $stream->streamEvents()->valid()) {
            return new JsonResponse('', 404);
        }

        $uri = $request->getUri();
        $id = $uri->getScheme() . '://' . $uri->getHost() . ':' . $uri->getPort() . '/streams/' . urlencode($streamName);

        $entries = [];
        foreach ($stream->streamEvents() as $event) {
            $entry = $this->messageConverter->convertToArray($event);
            $entry['created_at'] = $entry['created_at']->format('Y-m-d\TH:i:s.u');
            $entries[] = $entry;
        }

        $result = [
            'title' => "Event stream '$streamName'",
            'id' => $id,
            'streamName' => $streamName,
            'metadata' => $stream->metadata(),
            'links' => [
                [
                    'uri' => $id,
                    'relation' => 'self',
                ],
                [
                    'uri' => $id . '/head/backward/' . $count,
                    'relation' => 'first'
                ],
                [
                    'uri' => $id . '/1/forward/' . $count,
                    'relation' => 'last'
                ],
            ],
            'entries' => $entries,
        ];


        return new JsonResponse($result);
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
