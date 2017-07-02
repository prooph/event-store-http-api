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
use Prooph\EventStore\Http\Api\Action\FetchStreamNames;
use Prooph\EventStore\Http\Api\Container\Action\FetchStreamNamesFactory;
use Psr\Container\ContainerInterface;

class FetchStreamNamesFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_action_handler(): void
    {
        $eventStore = $this->prophesize(EventStore::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();

        $factory = new FetchStreamNamesFactory();

        $actionHandler = $factory($container->reveal());

        $this->assertInstanceOf(FetchStreamNames::class, $actionHandler);
    }
}
