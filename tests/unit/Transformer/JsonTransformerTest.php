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

namespace ProophTest\EventStore\Http\Api\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Zend\Diactoros\Response\JsonResponse;

class JsonTransformerTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_response(): void
    {
        $transformer = new JsonTransformer();

        $response = $transformer->createResponse(['foo' => 'bar']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(['foo' => 'bar'], json_decode($response->getBody()->getContents(), true));
    }
}
