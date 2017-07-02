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

namespace ProophTest\EventStore\Http\Api\Unit\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\FetchStreamNames;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Prooph\EventStore\Metadata\FieldType;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Metadata\Operator;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

class FetchStreamNamesTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_415_when_invalid_accept_header_sent(): void
    {
        $eventStore = $this->prophesize(EventStore::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchStreamNames($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(415, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_filtered_stream_names(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->fetchStreamNames('foo', new MetadataMatcher(), 20, 0)->willReturn(['foo', 'foobar'])->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/atom+json')->shouldBeCalled();
        $request->getAttribute('filter')->willReturn('foo')->shouldBeCalled();
        $request->getAttribute('limit')->willReturn('20')->shouldBeCalled();
        $request->getAttribute('offset')->willReturn('0')->shouldBeCalled();
        $request->getQueryParams()->willReturn([])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchStreamNames($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(['foo', 'foobar'], json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @test
     */
    public function it_will_return_all_stream_names_without_filter(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->fetchStreamNames(null, new MetadataMatcher(), 20, 0)->willReturn(['foo', 'foobar'])->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/atom+json')->shouldBeCalled();
        $request->getAttribute('filter')->willReturn(null)->shouldBeCalled();
        $request->getAttribute('limit')->willReturn('20')->shouldBeCalled();
        $request->getAttribute('offset')->willReturn('0')->shouldBeCalled();
        $request->getQueryParams()->willReturn([])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchStreamNames($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(['foo', 'foobar'], json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @test
     */
    public function it_respects_given_metadata_in_query_params(): void
    {
        $metadataMatcher = new MetadataMatcher();
        $metadataMatcher = $metadataMatcher->withMetadataMatch('foo', Operator::EQUALS(), 'bar', FieldType::METADATA());

        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->fetchStreamNames(null, $metadataMatcher, 20, 0)->willReturn(['foo', 'foobar'])->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/atom+json')->shouldBeCalled();
        $request->getAttribute('filter')->willReturn(null)->shouldBeCalled();
        $request->getAttribute('limit')->willReturn('20')->shouldBeCalled();
        $request->getAttribute('offset')->willReturn('0')->shouldBeCalled();
        $request->getQueryParams()->willReturn([
            'meta_0_field' => 'foo',
            'meta_0_operator' => 'EQUALS',
            'meta_0_value' => 'bar',
            'meta_1_field' => 'missing_parts',
            'meta_2_field' => 'invalid op',
            'meta_2_operator' => 'INVALID',
            'meta_2_value' => 'some value',
        ])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new FetchStreamNames($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame(['foo', 'foobar'], json_decode($response->getBody()->getContents(), true));
    }
}
