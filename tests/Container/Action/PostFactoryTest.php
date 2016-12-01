<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStore\Http\Api\Container\Action;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\Post;
use Prooph\EventStore\Http\Api\Container\Action\PostFactory;
use Prooph\EventStore\Http\Api\GenericEventFactory;

class PostFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_post_action(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EventStore::class)->willReturn($eventStore->reveal())->shouldBeCalled();
        $container->get(GenericEventFactory::class)->willReturn($messageFactory->reveal())->shouldBeCalled();

        $factory = new PostFactory();
        $stream = $factory->__invoke($container->reveal());

        $this->assertInstanceOf(Post::class, $stream);
    }
}
