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

namespace ProophTest\EventStore\Http\Api\Unit\Container\Action;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\FetchCategoryNamesRegex;
use Prooph\EventStore\Http\Api\Container\Action\FetchCategoryNamesRegexFactory;
use Psr\Container\ContainerInterface;

class FetchCategoryNamesRegexFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_action_handler(): void
    {
        $eventStore = $this->prophesize(EventStore::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();

        $factory = new FetchCategoryNamesRegexFactory();

        $actionHandler = $factory($container->reveal());

        $this->assertInstanceOf(FetchCategoryNamesRegex::class, $actionHandler);
    }
}
