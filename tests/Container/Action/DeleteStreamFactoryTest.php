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

namespace ProophTest\EventStore\Http\Api\Container\Action;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\DeleteStream;
use Prooph\EventStore\Http\Api\Container\Action\DeleteStreamFactory;
use Psr\Container\ContainerInterface;

class DeleteStreamFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_delete_stream_action(): void
    {
        $eventStore = $this->prophesize(EventStore::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();

        $factory = new DeleteStreamFactory();
        $stream = $factory->__invoke($container->reveal());

        $this->assertInstanceOf(DeleteStream::class, $stream);
    }
}
