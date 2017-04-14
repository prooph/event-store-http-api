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

namespace ProophTest\EventStore\Http\Api\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\Load;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Helper\UrlHelper;

class LoadTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_description_when_invalid_accept_header_sent(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageConverter = $this->prophesize(MessageConverter::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('')->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('foo\bar')->shouldBeCalled();
        $request->getUri()->willReturn(new Uri())->shouldBeCalled();

        $urlHelper = $this->prophesize(UrlHelper::class);

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new Load($eventStore->reveal(), $messageConverter->reveal(), $urlHelper->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/vnd.eventstore.atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents());

        $this->assertEquals('Description document for \'' . urldecode('foo\bar') . '\'', $json->title);
    }

    /**
     * @test
     */
    public function it_returns_400_on_bad_request(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageConverter = $this->prophesize(MessageConverter::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('foo')->shouldBeCalled();
        $request->getAttribute('start')->willReturn('head')->shouldBeCalled();
        $request->getAttribute('direction')->willReturn('forward')->shouldBeCalled();
        $request->getAttribute('count')->willReturn('1')->shouldBeCalled();

        $urlHelper = $this->prophesize(UrlHelper::class);

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new Load($eventStore->reveal(), $messageConverter->reveal(), $urlHelper->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/vnd.eventstore.atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEmpty(json_decode($response->getBody()->getContents()));
    }

    /**
     * @test
     */
    public function it_will_use_appropriate_transformer(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageConverter = $this->prophesize(MessageConverter::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('streamname')->willReturn('foo')->shouldBeCalled();
        $request->getAttribute('start')->willReturn('head')->shouldBeCalled();
        $request->getAttribute('direction')->willReturn('forward')->shouldBeCalled();
        $request->getAttribute('count')->willReturn('1')->shouldBeCalled();

        $urlHelper = $this->prophesize(UrlHelper::class);

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new Load($eventStore->reveal(), $messageConverter->reveal(), $urlHelper->reveal());

        $expectedResponses = [
            'application/vnd.eventstore.atom+json' => new JsonResponse(['transformer-1']),
            'application/vnd.eventstore.atom+html' => new HtmlResponse('<event></event>'),
        ];

        // Add all transformers to Load action.
        foreach ($expectedResponses as $forAcceptedValue => $expectedResponse) {
            $action->addTransformer(new TransformerStub($expectedResponse), $forAcceptedValue);
        }

        foreach ($expectedResponses as $forAcceptedValue => $expectedResponse) {
            $request->getHeaderLine('Accept')->willReturn($forAcceptedValue);

            $finalResponse = $action->process($request->reveal(), $delegate->reveal());

            $this->assertSame($expectedResponse, $finalResponse);
        }
    }
}
