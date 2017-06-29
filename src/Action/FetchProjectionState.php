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
use Prooph\EventStore\Exception\ProjectionNotFound;
use Prooph\EventStore\Http\Api\Transformer\Transformer;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

final class FetchProjectionState implements MiddlewareInterface
{
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

        $name = $request->getAttribute('name');

        try {
            $status = $this->projectionManager->fetchProjectionState($name);
        } catch (ProjectionNotFound $e) {
            return new EmptyResponse(404);
        }

        $transformer = $this->transformers[$request->getHeaderLine('Accept')];

        return $transformer->createResponse($status);
    }
}
