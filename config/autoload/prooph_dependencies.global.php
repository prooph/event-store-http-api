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

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'aliases' => [
            \Prooph\Common\Messaging\MessageConverter::class => \Prooph\Common\Messaging\NoOpMessageConverter::class,
        ],
        'factories' => [
            \Prooph\Common\Messaging\FQCNMessageFactory::class => InvokableFactory::class,
            \Prooph\Common\Messaging\NoOpMessageConverter::class => InvokableFactory::class,
            // for pdo adapter
            'Prooph\\EventStore\\Adapter\\PDO\\PDOEventStoreAdapter' => 'Prooph\\EventStore\\Adapter\\PDO\\Container\\PDOEventStoreAdapterFactory',
            'Prooph\\EventStore\\Adapter\\PDO\\JsonQuerier\\MySQL' => InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\PDO\\JsonQuerier\\Postgres' => InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\PDO\\IndexingStrategy\\MySQLAggregateStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\PDO\\IndexingStrategy\\MySQLSingleStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\PDO\\IndexingStrategy\\PostgresAggregateStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\PDO\\IndexingStrategy\\PostgresSingleStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\PDO\\TableNameGeneratorStrategy\\Sha1' => InvokableFactory::class,
        ],
    ],
];
