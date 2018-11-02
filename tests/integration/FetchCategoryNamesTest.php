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
class FetchCategoryNamesTest extends AbstractHttpApiServerTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        // drop user-123, user-234, blog-123, blog-234 tables
        $statement = $this->connection->prepare('DROP TABLE IF EXISTS _d5ecfb11836d0806d18f2fd4c815d970bdc54ddc;');
        $statement->execute();
        $statement = $this->connection->prepare('DROP TABLE IF EXISTS _e71a961cd5e091ca93860ebac1875b03b16e4033;');
        $statement->execute();
        $statement = $this->connection->prepare('DROP TABLE IF EXISTS _0605a4a1c33136fe58731aee83088a743b27265d;');
        $statement->execute();
        $statement = $this->connection->prepare('DROP TABLE IF EXISTS _fdb828d6d7be4ece3d06efc3b8b5ae12c61b5995;');
        $statement->execute();
    }

    /**
     * @test
     */
    public function it_fetches_category_names(): void
    {
        $this->createTestStreams();

        $this->fetchAllCategories();

        $this->fetchCategoriesFromOffset();

        $this->fetchCategoriesRegex();

        $this->fetchCategoriesFromOffset();
    }

    private function fetchAllCategories(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/categories',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["blog","user"]', $resBody);

        // test fetch stream with name user
        $request = new Request(
            'GET',
            'http://localhost:8080/categories/user',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["user"]', $resBody);
    }

    private function fetchCategoriesFromOffset(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/categories?limit=10&offset=1',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["user"]', $resBody);
    }

    private function fetchCategoriesRegex(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/categories-regex/^user',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["user"]', $resBody);
    }

    private function fetchCategoriesRegexFromOffset(): void
    {
        $request = new Request(
            'GET',
            'http://localhost:8080/categories-regex/^user|blog?limit=10&offset=1',
            [
                'Accept' => 'application/json',
            ]
        );

        $response = $this->client->sendRequest($request);

        $resBody = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode(), $resBody);
        $this->assertSame('["user"]', $resBody);
    }

    protected function createTestStreams(): void
    {
        foreach (['user-123', 'user-234', 'blog-123', 'blog-234'] as $streamName) {
            $request = new Request(
                'POST',
                'http://localhost:8080/stream/' . $streamName,
                [
                    'Content-Type' => 'application/vnd.eventstore.atom+json',
                ],
                '[
              {
                "uuid": "f9fea0b9-bbab-41ad-b3c1-56e09a1044a4",
                "created_at": "2016-11-12T14:35:41.702700",
                "message_name": "event-type",
                "payload": {
                  "a": "2"
                },
                "metadata":{"_aggregate_version":1}
              },
              {
                "message_name":"foo",
                "payload":{"b" : "c"},
                "metadata":{"_aggregate_version":2}
              },
              {
                "uuid": "8571b393-a4d6-4c82-be96-4371a2795d18",
                "created_at": "2016-11-12T14:35:41.705700",
                "message_name": "event-type"
              }
            ]'
            );

            $response = $this->client->sendRequest($request);

            $this->assertSame(204, $response->getStatusCode(), (string) $response->getBody());
        }
    }
}
