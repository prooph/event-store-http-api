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
class UpdateStreamMetadataTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_updates_stream_metadata(): void
    {
        $this->createTestStream();
        $this->updateStreamMetadata();

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
