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

namespace ProophTest\EventStore\Http\Api\Integration;

use GuzzleHttp\Psr7\Request;

/**
 * @group integration
 */
class FetchProjectionStateStatusStreamPositionsTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_fetches_projection_state_status_and_stream_positions(): void
    {
        $this->createTestStream();

        $this->createProjection();

        $this->waitForProjectionsToStart();

        $this->fetchProjectionStatus();
        $this->fetchProjectionState();
        $this->fetchProjectionStreamPositions();

        $this->fetchUnknownProjectionStatus();
        $this->fetchUnknownProjectionState();
        $this->fetchUnknownProjectionStreamPositions();
    }

    private function fetchProjectionStatus(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projection/status/test-projection',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('RUNNING', $response->getReasonPhrase());
    }

    private function fetchProjectionState(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projection/state/test-projection',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('{"counter":3}', $resBody);
    }

    private function fetchProjectionStreamPositions(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projection/stream-positions/test-projection',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('{"teststream":3}', $resBody);
    }

    private function fetchUnknownProjectionStatus(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projection/status/unknown',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode(), (string) $response->getBody());
    }

    private function fetchUnknownProjectionState(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projection/state/unknown',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode(), (string) $response->getBody());
    }

    private function fetchUnknownProjectionStreamPositions(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/projection/stream-positions/unknown',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode(), (string) $response->getBody());
    }
}
