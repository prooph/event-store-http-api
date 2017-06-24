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

use ArrayIterator;
use DateTimeImmutable;
use DateTimeZone;
use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\PostStream;
use Prooph\EventStore\Http\Api\GenericEvent;
use Prooph\EventStore\Http\Api\GenericEventFactory;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Prooph\EventStore\TransactionalEventStore;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Zend\Diactoros\Response\EmptyResponse;

class PostStreamTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_415_on_invalid_content_type(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('text/html')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(415, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_request_body(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn(['invalid body'])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Write request body invalid', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_request_body_2(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn('invalid')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Write request body invalid', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_missing_event_uuid(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Empty event uuid provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_event_uuid(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => 'invalid',
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Invalid event uuid provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_missing_event_name(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Empty event name provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_event_name(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => '',
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Invalid event name provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_missing_event_payload(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'event one',
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Empty event payload provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_event_payload(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'event one',
            'payload' => 'foo',
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Invalid event payload provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_missing_event_metadata(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'event one',
            'payload' => [],
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Empty event metadata provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_event_metadata(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => 'foo',
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Invalid event metadata provided', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_on_invalid_event_created_at(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $messageFactory = $this->prophesize(MessageFactory::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
            'created_at' => 'invalid',
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Invalid created at provided, expected format: Y-m-d\TH:i:s.u', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_returns_400_when_event_could_not_be_instantiated(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $time = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $eventStore = $this->prophesize(EventStore::class);

        $messageFactory = $this->prophesize(MessageFactory::class);
        $messageFactory->createMessageFromArray('event one', [
            'uuid' => $uuid,
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
            'created_at' => $time,
        ])->willThrow(new RuntimeException());

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => $uuid,
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
        ]])->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory->reveal());
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Could not create event instance', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_rolls_back_transaction_on_error_using_transactional_event_store(): void
    {
        $eventStore = $this->prophesize(TransactionalEventStore::class);
        $eventStore->beginTransaction()->shouldBeCalled();
        $eventStore->hasStream(new StreamName('test-stream'))->willReturn(false)->shouldBeCalled();
        $eventStore->create(new StreamName('test-stream'), Argument::type(ArrayIterator::class))->willThrow(new RuntimeException());
        $eventStore->rollback()->shouldBeCalled();

        $messageFactory = new GenericEventFactory();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
        ]])->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('test-stream')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory);
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Cannot create or append to stream', $response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function it_creates_stream_using_transactional_event_store(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $time = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $eventStore = $this->prophesize(TransactionalEventStore::class);
        $eventStore->beginTransaction()->shouldBeCalled();
        $eventStore->hasStream(new StreamName('test-stream'))->willReturn(false)->shouldBeCalled();
        $eventStore->create(
            new Stream(
                new StreamName('test-stream'),
                new ArrayIterator([
                    GenericEvent::fromArray([
                        'uuid' => $uuid,
                        'message_name' => 'event one',
                        'payload' => [],
                        'metadata' => [],
                        'created_at' => $time,
                    ]),
                ]),
                []
            )
        )->shouldBeCalled();
        $eventStore->commit()->shouldBeCalled();

        $messageFactory = new GenericEventFactory();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => $uuid,
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
            'created_at' => $time->format('Y-m-d\TH:i:s.u'),
        ]])->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('test-stream')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory);
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(204, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_appends_to_stream_using_transactional_event_store(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $time = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $eventStore = $this->prophesize(TransactionalEventStore::class);
        $eventStore->beginTransaction()->shouldBeCalled();
        $eventStore->hasStream(new StreamName('test-stream'))->willReturn(true)->shouldBeCalled();
        $eventStore->appendTo(
            new StreamName('test-stream'),
            new ArrayIterator([
                GenericEvent::fromArray([
                    'uuid' => $uuid,
                    'message_name' => 'event one',
                    'payload' => [],
                    'metadata' => [],
                    'created_at' => $time,
                ]),
            ])
        )->shouldBeCalled();
        $eventStore->commit()->shouldBeCalled();

        $messageFactory = new GenericEventFactory();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => $uuid,
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
            'created_at' => $time->format('Y-m-d\TH:i:s.u'),
        ]])->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('test-stream')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory);
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(204, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_creates_stream_using_non_transactional_event_store(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $time = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->hasStream(new StreamName('test-stream'))->willReturn(false)->shouldBeCalled();
        $eventStore->create(
            new Stream(
                new StreamName('test-stream'),
                new ArrayIterator([
                    GenericEvent::fromArray([
                        'uuid' => $uuid,
                        'message_name' => 'event one',
                        'payload' => [],
                        'metadata' => [],
                        'created_at' => $time,
                    ]),
                ]),
                []
            )
        )->shouldBeCalled();

        $messageFactory = new GenericEventFactory();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => $uuid,
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
            'created_at' => $time->format('Y-m-d\TH:i:s.u'),
        ]])->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('test-stream')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory);
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(204, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_appends_to_stream_using_non_transactional_event_store(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $time = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->hasStream(new StreamName('test-stream'))->willReturn(true)->shouldBeCalled();
        $eventStore->appendTo(
            new StreamName('test-stream'),
            new ArrayIterator([
                GenericEvent::fromArray([
                    'uuid' => $uuid,
                    'message_name' => 'event one',
                    'payload' => [],
                    'metadata' => [],
                    'created_at' => $time,
                ]),
            ])
        )->shouldBeCalled();

        $messageFactory = new GenericEventFactory();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Content-Type')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getParsedBody()->willReturn([[
            'uuid' => $uuid,
            'message_name' => 'event one',
            'payload' => [],
            'metadata' => [],
            'created_at' => $time->format('Y-m-d\TH:i:s.u'),
        ]])->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('test-stream')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new PostStream($eventStore->reveal(), $messageFactory);
        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertSame(204, $response->getStatusCode());
    }
}
