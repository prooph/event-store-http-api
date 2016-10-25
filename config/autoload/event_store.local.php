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

use Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter;
use Prooph\EventStore\Adapter\MongoDb\MongoDbEventStoreAdapter;

return [
    'prooph' => [
        'event_store' => [
            'default' => [
                'adapter' => [
                    'type' => MongoDbEventStoreAdapter::class,
                    'options' => [
                        'db_name' => 'dimabay_event_store',
                        'mongo_connection_alias' => 'mongo_client',
                    ]
                ],
//              'adapter' => [
//                  'type' => DoctrineEventStoreAdapter::class,
//                  'options' => [
//                      'connection_alias' => 'doctrine.connection.default',
//              ],
            ],
        ],
    ],
];
