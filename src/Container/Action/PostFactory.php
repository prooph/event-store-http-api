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

namespace Prooph\EventStore\Http\Api\Container\Action;

use Interop\Container\ContainerInterface;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\Post;
use Prooph\EventStore\Http\Api\GenericEventFactory;

final class PostFactory
{
    public function __invoke(ContainerInterface $container): Post
    {
        return new Post($container->get(EventStore::class), $container->get(GenericEventFactory::class));
    }
}
