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
use Prooph\EventStore\Http\Api\Action\FetchProjectionState;
use Prooph\EventStore\Http\Api\Container\Action\FetchProjectionStateFactory;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Container\ContainerInterface;

class FetchProjectionStateFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_action_handler(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(ProjectionManager::class)->willReturn($projectionManager->reveal())->shouldBeCalled();

        $factory = new FetchProjectionStateFactory();

        $actionHandler = $factory($container->reveal());

        $this->assertInstanceOf(FetchProjectionState::class, $actionHandler);
    }
}
