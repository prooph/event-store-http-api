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
class DeleteProjectionTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_receives_error_deleting_non_existing_projection(): void
    {
        $request = new Request('POST', 'http://localhost:8080/projection/delete/test-projection/true');

        $response = $this->client->sendRequest($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_deletes_existing_projections(): void
    {
        $this->createProjection();

        $this->createReadModelProjection();

        $this->waitForProjectionsToStart();

        $request = new Request('POST', 'http://localhost:8080/projection/delete/test-projection/true');

        $response = $this->client->sendRequest($request);

        $this->assertSame(204, $response->getStatusCode());

        $request = new Request('POST', 'http://localhost:8080/projection/delete/test-readmodel-projection/true');

        $response = $this->client->sendRequest($request);

        $this->assertSame(204, $response->getStatusCode());
    }
}
