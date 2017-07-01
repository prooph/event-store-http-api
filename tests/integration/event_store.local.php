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

namespace Prooph\EventStore\Http\Api;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\Container\PdoConnectionFactory;
use Prooph\EventStore\Pdo\Container\PostgresEventStoreFactory;
use Prooph\EventStore\Pdo\PersistenceStrategy\PostgresAggregateStreamStrategy;

return [
    'dependencies' => [
        'factories' => [
            EventStore::class => PostgresEventStoreFactory::class,
            'pdo_connection' => PdoConnectionFactory::class,
        ],
    ],
    'prooph' => [
        'event_store' => [
            'default' => [
                'connection' => 'pdo_connection',
                'message_factory' => GenericEventFactory::class,
                'persistence_strategy' => PostgresAggregateStreamStrategy::class,
            ],
        ],
        'pdo_connection' => [
            'default' => [
                'schema' => 'pgsql',
                'user' => 'postgres',
                'password' => 'postgres',
                'host' => 'localhost',
                'dbname' => 'event_store_http_api',
                'port' => 5432,
            ],
        ],
    ],
];
