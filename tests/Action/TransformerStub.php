<?php

declare(strict_types=1);
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProophTest\EventStore\Http\Api\Action;

use Prooph\EventStore\Http\Api\Transformer\Transformer;
use Psr\Http\Message\ResponseInterface;

final class TransformerStub implements Transformer
{
    /**
     * @var ResponseInterface
     */
    private $result;

    /**
     * @param ResponseInterface $result
     */
    public function __construct(ResponseInterface $result)
    {
        $this->result = $result;
    }

    /**
     * @param array $result
     * @return ResponseInterface
     */
    public function stream(array $result): ResponseInterface
    {
        return $this->result;
    }

    /**
     * @param string $message
     * @param int $code
     * @return ResponseInterface
     */
    public function error(string $message, int $code): ResponseInterface
    {
        return $this->result;
    }
}
