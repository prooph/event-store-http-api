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

namespace ProophTest\EventStore\Http\Api\Unit\Container\Action;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\HasStream;
use Prooph\EventStore\Http\Api\Container\Action\HasStreamFactory;
use Psr\Container\ContainerInterface;

class HasStreamFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_fetch_stream_metadata_action(): void
    {
        $eventStore = $this->prophesize(EventStore::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();

        $factory = new HasStreamFactory();
        $stream = $factory->__invoke($container->reveal());

        $this->assertInstanceOf(HasStream::class, $stream);
    }
}
