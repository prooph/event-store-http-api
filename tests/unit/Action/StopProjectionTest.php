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

namespace ProophTest\EventStore\Http\Api\Unit\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Exception\ProjectionNotFound;
use Prooph\EventStore\Http\Api\Action\StopProjection;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class StopProjectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_will_delete_projection(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->stopProjection('runner')->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('name')->willReturn('runner')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new StopProjection($projectionManager->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_404_when_unknown_projection_asked(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->stopProjection('runner')->willThrow(new ProjectionNotFound())->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('name')->willReturn('runner')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new StopProjection($projectionManager->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
