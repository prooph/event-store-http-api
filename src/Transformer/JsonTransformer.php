<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Transformer;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

final class JsonTransformer implements Transformer
{
    /**
     * @param array $result
     * @return ResponseInterface
     */
    public function stream(array $result): ResponseInterface
    {
        return new JsonResponse($result);
    }

    /**
     * @param string $message
     * @param int $code
     * @return ResponseInterface
     */
    public function error(string $message, int $code): ResponseInterface
    {
        return new JsonResponse($message, $code);
    }
}
