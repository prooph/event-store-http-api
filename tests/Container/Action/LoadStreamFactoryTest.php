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
use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\LoadStream;
use Prooph\EventStore\Http\Api\Container\Action\LoadStreamFactory;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

class LoadStreamFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_load_action(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageConverter = $this->prophesize(MessageConverter::class);
        $urlHelper = $this->prophesize(UrlHelper::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();
        $container->get(MessageConverter::class)->willReturn($messageConverter->reveal())->shouldBeCalled();
        $container->get(UrlHelper::class)->willReturn($urlHelper->reveal())->shouldBeCalled();

        $factory = new LoadStreamFactory();
        $stream = $factory->__invoke($container->reveal());

        $this->assertInstanceOf(LoadStream::class, $stream);
    }
}
