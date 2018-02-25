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
use Prooph\EventStore\Http\Api\Action\FetchProjectionStreamPositions;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

class FetchProjectionStreamPositionsTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_415_when_invalid_accept_header_sent(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchProjectionStreamPositions($projectionManager->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(415, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_stream_positions(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->fetchProjectionStreamPositions('foo')->willReturn(['foo' => 100, 'bar' => 200])->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/atom+json')->shouldBeCalled();
        $request->getAttribute('name')->willReturn('foo')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchProjectionStreamPositions($projectionManager->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(['foo' => 100, 'bar' => 200], json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @test
     */
    public function it_returns_404_on_unknown_projection(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->fetchProjectionStreamPositions('unknown')->willThrow(ProjectionNotFound::withName('unknown'))->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/atom+json')->shouldBeCalled();
        $request->getAttribute('name')->willReturn('unknown')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchProjectionStreamPositions($projectionManager->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
