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

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\Exception\StreamNotFound;
use Prooph\EventStore\Http\Api\Model\MetadataMatcherBuilder;
use Prooph\EventStore\Http\Api\Transformer\Transformer;
use Prooph\EventStore\ReadOnlyEventStore;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;

final class LoadStream implements MiddlewareInterface
{
    /**
     * @var ReadOnlyEventStore
     */
    private $eventStore;

    /**
     * @var MessageConverter
     */
    private $messageConverter;

    /**
     * @var Transformer[]
     */
    private $transformers = [];

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(ReadOnlyEventStore $eventStore, MessageConverter $messageConverter, UrlHelper $urlHelper)
    {
        $this->eventStore = $eventStore;
        $this->messageConverter = $messageConverter;
        $this->urlHelper = $urlHelper;
    }

    public function addTransformer(Transformer $transformer, string ...$names)
    {
        foreach ($names as $name) {
            $this->transformers[$name] = $transformer;
        }
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $streamName = urldecode($request->getAttribute('streamname'));

        if (! array_key_exists($request->getHeaderLine('Accept'), $this->transformers)) {
            return $this->returnDescription($request, $streamName);
        }

        $transformer = $this->transformers[$request->getHeaderLine('Accept')];

        $start = $request->getAttribute('start');

        if ('head' === $start) {
            $start = PHP_INT_MAX;
        }

        $start = (int) $start;

        $count = (int) $request->getAttribute('count');

        $direction = $request->getAttribute('direction');

        if (PHP_INT_MAX === $start && 'forward' === $direction) {
            return new EmptyResponse(400);
        }

        $metadataMatcherBuilder = new MetadataMatcherBuilder();
        $metadataMatcher = $metadataMatcherBuilder->createMetadataMatcherFrom($request, true);

        try {
            if ($direction === 'backward') {
                $streamEvents = $this->eventStore->loadReverse(new StreamName($streamName), $start, $count, $metadataMatcher);
            } else {
                $streamEvents = $this->eventStore->load(new StreamName($streamName), $start, $count, $metadataMatcher);
            }
        } catch (StreamNotFound $e) {
            return new EmptyResponse(404);
        }

        if (! $streamEvents->valid()) {
            $response = new EmptyResponse();

            return $response->withStatus(400, '\'' . $start . '\' is not a valid event number');
        }

        $entries = [];

        foreach ($streamEvents as $event) {
            $entry = $this->messageConverter->convertToArray($event);
            $entry['created_at'] = $entry['created_at']->format('Y-m-d\TH:i:s.u');
            $entries[] = $entry;
        }

        $host = $this->host($request);

        $id = $host . $this->urlHelper->generate('page::query-stream', [
            'streamname' => urlencode($streamName),
        ]);

        $result = [
            'title' => "Event stream '$streamName'",
            'id' => $id,
            'streamName' => $streamName,
            'links' => [
                [
                    'uri' => $id,
                    'relation' => 'self',
                ],
                [
                    'uri' => $host . $this->urlHelper->generate('page::query-stream', [
                        'streamname' => urlencode($streamName),
                        'start' => '1',
                        'direction' => 'forward',
                        'count' => $count,
                    ]),
                    'relation' => 'first',
                ],
                [
                    'uri' => $host . $this->urlHelper->generate('page::query-stream', [
                        'streamname' => urlencode($streamName),
                        'start' => 'head',
                        'direction' => 'backward',
                        'count' => $count,
                    ]),
                    'relation' => 'last',
                ],
            ],
            'entries' => $entries,
        ];

        return $transformer->createResponse($result);
    }

    private function returnDescription(ServerRequestInterface $request, string $streamName): JsonResponse
    {
        $id = $this->host($request) . $this->urlHelper->generate('page::query-stream', [
            'streamname' => urlencode($streamName),
        ]);

        return new JsonResponse(
            [
                'title' => 'Description document for \'' . $streamName . '\'',
                'description' => 'The description document will be presented when no accept header is present or it was requested',
                '_links' => [
                    'self' => [
                        'href' => $id,
                        'supportedContentTypes' => [
                            'application/vnd.eventstore.streamdesc+json',
                        ],
                    ],
                    'stream' => [
                        'href' => $id,
                        'supportedContentTypes' => array_keys($this->transformers),
                    ],
                ],
            ],
            200,
            [
                'Content-Type' => 'application/vnd.eventstore.streamdesc+json; charset=utf-8',
            ]
        );
    }

    private function host(ServerRequestInterface $request): string
    {
        $uri = $request->getUri();
        $host = $uri->getScheme() . '://' . $uri->getHost();

        if (null !== $uri->getPort()) {
            $host .= ':' . $uri->getPort();
        }

        return $host;
    }
}
