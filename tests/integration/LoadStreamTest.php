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
class LoadStreamTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_receives_description_when_accept_header_not_set(): void
    {
        $this->createTestStream();

        $this->loadWithoutAcceptHeader();

        $this->simpleLoad();
    }

    private function loadWithoutAcceptHeader(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/stream/teststream'
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
        $json = $response->getBody()->getContents();

        $this->assertJson($json);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('_links', $data);

        $this->assertSame('Description document for \'teststream\'', $data['title']);
        $this->assertSame('The description document will be presented when no accept header is present or it was requested', $data['description']);
    }

    private function simpleLoad(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/stream/teststream',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());

        $json = $response->getBody()->getContents();

        $this->assertJson($json);

        $data = json_decode($json, true);

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('streamName', $data);
        $this->assertArrayHasKey('_links', $data);
        $this->assertArrayHasKey('entries', $data);

        $this->assertSame('Event stream \'teststream\'', $data['title']);
        $this->assertSame('http://localhost:8080/stream/teststream/1/forward/10', $data['id']);
        $this->assertSame('teststream', $data['streamName']);
        $this->assertSame(
            [
                [
                    'uri' => 'http://localhost:8080/stream/teststream/1/forward/10',
                    'relation' => 'self',
                ],
                [
                    'uri' => 'http://localhost:8080/stream/teststream/1/forward/10',
                    'relation' => 'first',
                ],
                [
                    'uri' => 'http://localhost:8080/stream/teststream/head/backward/10',
                    'relation' => 'last',
                ],
            ],
            $data['_links']
        );
        $this->assertCount(3, $data['entries']);
    }
}
