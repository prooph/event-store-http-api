<?php

/**
 * This file is part of prooph/event-store-http-api.
 * (c) 2016-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStore\Http\Api\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Http\Api\Middleware\BaseUrl;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Helper\UrlHelper;

class BaseUrlTest extends TestCase
{
    /**
     * @test
     */
    public function it_delegates_request(): void
    {
        $urlHelper = $this->prophesize(UrlHelper::class);
        $urlHelper->setBasePath('/http-api')->shouldBeCalled();

        $finalUri = $this->prophesize(Uri::class);

        $uri = $this->prophesize(Uri::class);
        $uri->getPath()->willReturn('/http-api/foo')->shouldBeCalled();
        $uri->withPath('/foo')->willReturn($finalUri->reveal());

        $finalRequest = $this->prophesize(ServerRequestInterface::class);
        $finalRequest = $finalRequest->reveal();

        $modifiedRequest = $this->prophesize(ServerRequestInterface::class);
        $modifiedRequest->withUri($finalUri)->willReturn($finalRequest)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn($uri->reveal());
        $request->withAttribute(BaseUrl::BASE_URL, '/http-api')->willReturn($modifiedRequest->reveal())->shouldBeCalled();

        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($finalRequest)->willReturn(new JsonResponse(''))->shouldBeCalled();

        $middleware = new BaseUrl('/http-api', $urlHelper->reveal());
        $response = $middleware->process($request->reveal(), $handler->reveal());

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @test
     */
    public function it_delegates_request_early_if_path_doesnt_match(): void
    {
        $urlHelper = $this->prophesize(UrlHelper::class);

        $uri = $this->prophesize(Uri::class);
        $uri->getPath()->willReturn('/')->shouldBeCalled();

        $modifiedRequest = $this->prophesize(ServerRequestInterface::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn($uri->reveal());
        $request->withAttribute(BaseUrl::BASE_URL, '/')->willReturn($modifiedRequest->reveal())->shouldBeCalled();

        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($modifiedRequest)->willReturn(new JsonResponse(''))->shouldBeCalled();

        $middleware = new BaseUrl('/', $urlHelper->reveal());
        $response = $middleware->process($request->reveal(), $handler->reveal());

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
