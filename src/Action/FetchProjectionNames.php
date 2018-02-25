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
use Prooph\EventStore\Http\Api\Transformer\Transformer;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

final class FetchProjectionNames implements MiddlewareInterface
{
    private const DEFAULT_LIMIT = 20;
    private const DEFAULT_OFFSET = 0;

    /**
     * @var ProjectionManager
     */
    private $projectionManager;

    /**
     * @var Transformer[]
     */
    private $transformers = [];

    public function __construct(ProjectionManager $projectionManager)
    {
        $this->projectionManager = $projectionManager;
    }

    public function addTransformer(Transformer $transformer, string ...$names)
    {
        foreach ($names as $name) {
            $this->transformers[$name] = $transformer;
        }
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (! array_key_exists($request->getHeaderLine('Accept'), $this->transformers)) {
            return new EmptyResponse(415);
        }

        $filter = $request->getAttribute('filter');

        if (null !== $filter) {
            $filter = urldecode($filter);
        }

        $queryParams = $request->getQueryParams();

        $limit = $queryParams['limit'] ?? self::DEFAULT_LIMIT;
        $offset = $queryParams['offset'] ?? self::DEFAULT_OFFSET;

        $projectionNames = $this->projectionManager->fetchProjectionNames($filter, (int) $limit, (int) $offset);

        $transformer = $this->transformers[$request->getHeaderLine('Accept')];

        return $transformer->createResponse($projectionNames);
    }
}
