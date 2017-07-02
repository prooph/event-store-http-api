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

namespace Prooph\EventStore\Http\Api\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Prooph\EventStore\Http\Api\Transformer\Transformer;
use Prooph\EventStore\ReadOnlyEventStore;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

final class HasStream implements MiddlewareInterface
{
    /**
     * @var ReadOnlyEventStore
     */
    private $eventStore;

    /**
     * @var Transformer[]
     */
    private $transformers = [];

    public function __construct(ReadOnlyEventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $streamName = urldecode($request->getAttribute('streamname'));

        if ($this->eventStore->hasStream(new StreamName($streamName))) {
            return new EmptyResponse(200);
        }

        return new EmptyResponse(404);
    }
}
