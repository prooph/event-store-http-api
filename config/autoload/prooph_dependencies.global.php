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

return [
    'dependencies' => [
        'factories' => [
            \Prooph\Common\Messaging\FQCNMessageFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            'Prooph\\EventStore\\Adapter\\MongoDb\\MongoDbEventStoreAdapter' => 'Prooph\\EventStore\\Adapter\\MongoDb\\Container\\MongoDbEventStoreAdapterFactory',
            'Prooph\\EventStore\\Adapter\\Doctrine\\DoctrineEventStoreAdapter' => 'Prooph\\EventStore\\Adapter\\Doctrine\\Container\\DoctrineEventStoreAdapterFactory',
        ],
    ],
];
