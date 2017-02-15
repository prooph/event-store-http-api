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

namespace Prooph\EventStore\Http\Api\Container\Action;

use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\Load;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Psr\Container\ContainerInterface;

final class LoadFactory
{
    public function __invoke(ContainerInterface $container): Load
    {
        $actionHandler = new Load($container->get(EventStore::class), $container->get(MessageConverter::class));

        $actionHandler->addTransformer(
            new JsonTransformer(),
            'application/vnd.eventstore.atom+json',
            'application/atom+json',
            'application/json'
        );

        return $actionHandler;
    }
}
