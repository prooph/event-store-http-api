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

namespace ProophTest\EventStore\Http\Api\Transformer;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Zend\Diactoros\Response\JsonResponse;

class JsonTransformerTest extends TestCase
{
    /**
     * @var JsonTransformer
     */
    private $transformer;

    protected function setUp(): void
    {
        $this->transformer = new JsonTransformer();
    }

    /**
     * @test
     */
    public function it_streams_response(): void
    {
        $response = $this->transformer->stream(['foo' => 'bar']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(['foo' => 'bar'], json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @test
     */
    public function it_returns_error(): void
    {
        $response = $this->transformer->error('foo', 400);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('"foo"', $response->getBody()->getContents());
    }
}
