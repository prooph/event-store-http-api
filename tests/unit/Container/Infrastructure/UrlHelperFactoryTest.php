<?php

/**
 * This file is part of prooph/event-store-http-api.
 * (c) 2016-2019 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2016-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStore\Http\Api\Unit\Container\Infrastructure;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Http\Api\Container\Infrastructure\UrlHelperFactory;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper as ZendUrlHelper;

class UrlHelperFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_wraps_zend_url_helper()
    {
        $zendUrlHelper = $this->prophesize(ZendUrlHelper::class);

        $zendUrlHelper->generate(Argument::exact('user'), Argument::exact(['id' => 1]))->willReturn('/user/1')->shouldBeCalled();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get(Argument::exact(ZendUrlHelper::class))->willReturn($zendUrlHelper->reveal())->shouldBeCalled();

        $factory = new UrlHelperFactory();

        $url = $factory->__invoke($container->reveal())->generate('user', ['id' => 1]);

        $this->assertSame('/user/1', $url);
    }
}
