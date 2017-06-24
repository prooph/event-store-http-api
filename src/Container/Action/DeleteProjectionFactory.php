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

use Prooph\EventStore\Http\Api\Action\DeleteProjection;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Container\ContainerInterface;

final class DeleteProjectionFactory
{
    public function __invoke(ContainerInterface $container): DeleteProjection
    {
        return new DeleteProjection($container->get(ProjectionManager::class));
    }
}
