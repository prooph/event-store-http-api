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

namespace ProophTest\EventStore\Http\Api\Integration;

use GuzzleHttp\Psr7\Request;

/**
 * @group integration
 */
class FetchProjectionNamesTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_fetches_stream_names(): void
    {
        $this->createTestStream();

        $this->createProjection();

        $this->waitForProjectionsToStart();

        $this->fetchAllProjections();

        $this->fetchProjectionWithName();

        $this->fetchUnknownProjectionWithName();

        $this->fetchProjectionsFromOffset();

        $this->fetchProjectionsRegex();

        $this->fetchUnknownProjectionRegex();
    }

    private function fetchAllProjections(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projections',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["test-projection"]', $resBody);
    }

    private function fetchProjectionWithName(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projections/test-projection',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["test-projection"]', $resBody);
    }

    private function fetchUnknownProjectionWithName(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projections/foo',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('[]', $resBody);
    }

    private function fetchProjectionsFromOffset(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projections?limit=10&offset=10',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('[]', $resBody);
    }

    private function fetchProjectionsRegex(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projections-regex/^test',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["test-projection"]', $resBody);
    }

    private function fetchUnknownProjectionRegex(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projections-regex/^foo',
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
