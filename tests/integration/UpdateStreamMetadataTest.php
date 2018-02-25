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
class UpdateStreamMetadataTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_updates_stream_metadata(): void
    {
        $this->createTestStream();

        $request = new Request('GET', 'http://localhost:8080/streammetadata/teststream', [
            'Accept' => 'application/vnd.eventstore.atom+json',
        ]);

        $response = $this->client->sendRequest($request);

        $this->assertSame('[]', $response->getBody()->getContents());

        $request = new Request(
            'POST',
            'http://localhost:8080/streammetadata/teststream',
            [
                'Content-Type' => 'application/vnd.eventstore.atom+json',
            ],
            '[
              {"foo": "bar"},
              {"foobar": "baz"}
            ]'
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(204, $response->getStatusCode());

        $request = new Request('GET', 'http://localhost:8080/streammetadata/teststream', [
            'Accept' => 'application/vnd.eventstore.atom+json',
        ]);

        $response = $this->client->sendRequest($request);

        $this->assertSame(
            '[{"foo":"bar"},{"foobar":"baz"}]',
            $response->getBody()->getContents()
        );

        $request = new Request(
            'POST',
            'http://localhost:8080/streammetadata/unknown',
            [
                'Content-Type' => 'application/vnd.eventstore.atom+json',
            ],
            '[
              {"foo": "bar"},
              {"foobar": "baz"}
            ]'
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode());
    }
}
