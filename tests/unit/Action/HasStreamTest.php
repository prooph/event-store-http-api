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
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\HasStream;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class HasStreamTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_404_when_stream_not_found(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->hasStream(new StreamName('unknown'))->willReturn(false)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('streamname')->willReturn('unknown')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new HasStream($eventStore->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_200_when_stream_found(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->hasStream(new StreamName('known'))->willReturn(true)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('streamname')->willReturn('known')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new HasStream($eventStore->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
