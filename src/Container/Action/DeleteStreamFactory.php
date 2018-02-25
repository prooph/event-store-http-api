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

namespace Prooph\EventStore\Http\Api\Container\Action;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\DeleteStream;
use Psr\Container\ContainerInterface;

final class DeleteStreamFactory
{
    public function __invoke(ContainerInterface $container): DeleteStream
    {
        return new DeleteStream($container->get(EventStore::class));
    }
}
