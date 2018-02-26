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
use Prooph\EventStore\Exception\ProjectionNotFound;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

final class DeleteProjection implements MiddlewareInterface
{
    /**
     * @var ProjectionManager
     */
    private $projectionManager;

    public function __construct(ProjectionManager $projectionManager)
    {
        $this->projectionManager = $projectionManager;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $projectionName = urldecode($request->getAttribute('name'));
        $deleteEmittedEvents = $request->getAttribute('deleteEmittedEvents');

        switch ($deleteEmittedEvents) {
            case 'false':
                $deleteEmittedEvents = false;
                break;
            case 'true':
                $deleteEmittedEvents = true;
                break;
        }

        try {
            $this->projectionManager->deleteProjection($projectionName, $deleteEmittedEvents);
        } catch (ProjectionNotFound $e) {
            return new EmptyResponse(404);
        }

        return new EmptyResponse(204);
    }
}
