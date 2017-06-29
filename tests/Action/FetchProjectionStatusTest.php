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

namespace ProophTest\EventStore\Http\Api\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Exception\ProjectionNotFound;
use Prooph\EventStore\Http\Api\Action\FetchProjectionStatus;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ProjectionStatus;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class FetchProjectionStatusTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_status(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->fetchProjectionStatus('foo')->willReturn(ProjectionStatus::RUNNING())->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('name')->willReturn('foo')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchProjectionStatus($projectionManager->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(ProjectionStatus::RUNNING()->getName(), $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_404_on_unknown_projection(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->fetchProjectionStatus('unknown')->willThrow(ProjectionNotFound::withName('unknown'))->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('name')->willReturn('unknown')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchProjectionStatus($projectionManager->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
