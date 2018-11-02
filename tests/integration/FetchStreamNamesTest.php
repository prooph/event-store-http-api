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

namespace ProophTest\EventStore\Http\Api\Integration;

use GuzzleHttp\Psr7\Request;

/**
 * @group integration
 */
class FetchStreamNamesTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_fetches_stream_names(): void
    {
        $this->createTestStream();

        $this->fetchAllStreams();

        $this->fetchStreamWithName();

        $this->fetchUnknownStreamWithName();

        $this->fetchStreamsFromOffset();

        $this->fetchStreamsRegex();

        $this->fetchUnknownStreamsRegex();
    }

    private function fetchAllStreams(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/streams',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["teststream"]', $resBody);
    }

    private function fetchStreamWithName(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/streams/teststream',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["teststream"]', $resBody);
    }

    private function fetchUnknownStreamWithName(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/streams/foo',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('[]', $resBody);
    }

    private function fetchStreamsFromOffset(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/streams?limit=10&offset=10',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('[]', $resBody);
    }

    private function fetchStreamsRegex(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/streams-regex/^test',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["teststream"]', $resBody);
    }

    private function fetchUnknownStreamsRegex(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/streams-regex/^foo',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('[]', $resBody);
    }
}
