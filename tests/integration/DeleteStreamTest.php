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
class DeleteStreamTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_deletes_stream(): void
    {
        $this->createTestStream();

        $request = new Request('POST', 'http://localhost:8080/delete/teststream');

        $response = $this->client->sendRequest($request);

        $this->assertSame(204, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * @test
     */
    public function it_cannot_delete_unknown_stream(): void
    {
        $request = new Request('POST', 'http://localhost:8080/delete/unknown');

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode(), (string) $response->getBody());
    }
}
