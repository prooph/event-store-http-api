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
class HasStreamTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_checks_for_existing_stream(): void
    {
        $request = new Request('GET', 'http://localhost:8080/has-stream/teststream');

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode());

        $this->createTestStream();

        $request = new Request('GET', 'http://localhost:8080/has-stream/teststream');

        $response = $this->client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
    }
}
