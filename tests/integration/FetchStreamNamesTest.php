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

        // test fetch all stream
        $request = new Request(
            'GET',
            'http://localhost:8080/streams',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('["teststream"]', $response->getBody()->getContents());

        // test fetch stream with name teststream
        $request = new Request(
            'GET',
            'http://localhost:8080/streams/teststream',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('["teststream"]', $response->getBody()->getContents());

        // test fetch stream with name foo
        $request = new Request(
            'GET',
            'http://localhost:8080/streams/foo',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[]', $response->getBody()->getContents());

        // test fetch all streams from offset 10
        $request = new Request(
            'GET',
            'http://localhost:8080/streams/10/10',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        // test fetch stream with regex ^foo
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[]', $response->getBody()->getContents());

        $request = new Request(
            'GET',
            'http://localhost:8080/streams-regex/^foo',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[]', $response->getBody()->getContents());

        // test fetch stream with regex ^test
        $request = new Request(
            'GET',
            'http://localhost:8080/streams-regex/^test',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('["teststream"]', $response->getBody()->getContents());
    }
}
