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

namespace Prooph\EventStore\Http\Api;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Middleware\GenericEventFactory;
use Prooph\EventStore\Pdo\Container\PdoConnectionFactory;
use Prooph\EventStore\Pdo\Container\PostgresEventStoreFactory;
use Prooph\EventStore\Pdo\Container\PostgresProjectionManagerFactory;
use Prooph\EventStore\Pdo\PersistenceStrategy\PostgresSimpleStreamStrategy;
use Prooph\EventStore\Projection\ProjectionManager;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'factories' => [
            EventStore::class => PostgresEventStoreFactory::class,
            'pdo_connection' => PdoConnectionFactory::class,
            PostgresSimpleStreamStrategy::class => InvokableFactory::class,
            ProjectionManager::class => PostgresProjectionManagerFactory::class,
        ],
    ],
    'prooph' => [
        'event_store' => [
            'default' => [
                'connection' => 'pdo_connection',
                'message_factory' => GenericEventFactory::class,
                'persistence_strategy' => PostgresSimpleStreamStrategy::class,
            ],
        ],
        'pdo_connection' => [
            'default' => [
                'schema' => \substr(\getenv('DB_DRIVER'), 4),
                'user' => \getenv('DB_USERNAME'),
                'password' => \getenv('DB_PASSWORD'),
                'host' => \getenv('DB_HOST'),
                'dbname' => \getenv('DB_NAME'),
                'port' => \getenv('DB_PORT'),
                'charset' => \getenv('DB_CHARSET'),
            ],
        ],
        'projection_manager' => [
            'default' => [
                'connection' => 'pdo_connection',
            ],
        ],
    ],
];
