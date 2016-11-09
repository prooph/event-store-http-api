<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProophTest\EventStore\Http\Api\Container\Action;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\Stream;
use Prooph\EventStore\Http\Api\Container\Action\StreamFactory;

class StreamFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_stream_action(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageConverter = $this->prophesize(MessageConverter::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();
        $container->get(MessageConverter::class)->willReturn($messageConverter->reveal())->shouldBeCalled();

        $factory = new StreamFactory();
        $stream = $factory->__invoke($container->reveal());

        $this->assertInstanceOf(Stream::class, $stream);
    }
}
