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

namespace ProophTest\EventStore\Http\Api\Unit\Container\Middleware;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Http\Api\Container\Middleware\BaseUrlFactory;
use Prooph\EventStore\Http\Api\Middleware\BaseUrl;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

class BaseUrlFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_base_url_middleware(): void
    {
        $urlHelper = $this->prophesize(UrlHelper::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(UrlHelper::class)->willReturn($urlHelper->reveal())->shouldBeCalled();
        $container->get('config')->willReturn(['http' => ['base_url' => '/http-api']])->shouldBeCalled();

        $factory = new BaseUrlFactory();

        $middleware = $factory($container->reveal());

        $this->assertInstanceOf(BaseUrl::class, $middleware);
    }

    /**
     * @test
     */
    public function it_creates_base_url_middleware_with_default_base_url(): void
    {
        $urlHelper = $this->prophesize(UrlHelper::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(UrlHelper::class)->willReturn($urlHelper->reveal())->shouldBeCalled();
        $container->get('config')->willReturn([])->shouldBeCalled();

        $factory = new BaseUrlFactory();

        $middleware = $factory($container->reveal());

        $this->assertInstanceOf(BaseUrl::class, $middleware);
    }
}
