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
use Prooph\EventStore\Http\Api\Action\FetchProjectionNamesRegex;
use Prooph\EventStore\Http\Api\Container\Action\FetchProjectionNamesRegexFactory;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Container\ContainerInterface;

class FetchProjectionNamesRegexFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_action_handler(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(ProjectionManager::class)->willReturn($projectionManager->reveal())->shouldBeCalled();

        $factory = new FetchProjectionNamesRegexFactory();

        $actionHandler = $factory($container->reveal());

        $this->assertInstanceOf(FetchProjectionNamesRegex::class, $actionHandler);
    }
}
