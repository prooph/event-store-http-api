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

namespace Prooph\EventStore\Http\Api;

use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\Container\EventStoreFactory;
use Prooph\EventStore\EventStore;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'aliases' => [
            MessageFactory::class => GenericEventFactory::class,
        ],
        'factories' => [
            Application::class => ApplicationFactory::class,
            EventStore::class => EventStoreFactory::class,
            GenericEventFactory::class => InvokableFactory::class,
            // actions
            Action\Load::class => Container\Action\LoadFactory::class,
        ],
    ],
];
