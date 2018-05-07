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

namespace ProophTest\EventStore\Http\Api\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Http\Api\Middleware\ResponseFactory;

class ResponseFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_response_object()
    {
        $response = (new ResponseFactory())->createResponse(201);

        $this->assertSame(201, $response->getStatusCode());
    }
}
