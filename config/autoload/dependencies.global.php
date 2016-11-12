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
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'aliases' => [
            \Prooph\Common\Messaging\MessageConverter::class => \Prooph\Common\Messaging\NoOpMessageConverter::class,
        ],
        'factories' => [
            // app
            Application::class => ApplicationFactory::class,
            GenericEventFactory::class => InvokableFactory::class,
            // actions
            Action\Load::class => Container\Action\LoadFactory::class,
            Action\Post::class => Container\Action\PostFactory::class,
            // prooph
            \Prooph\Common\Messaging\FQCNMessageFactory::class => InvokableFactory::class,
            \Prooph\Common\Messaging\NoOpMessageConverter::class => InvokableFactory::class,
            // for pdo adapter
            'Prooph\\EventStore\\PDO\\IndexingStrategy\\MySQLAggregateStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\PDO\\IndexingStrategy\\MySQLSingleStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\PDO\\IndexingStrategy\\PostgresAggregateStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\PDO\\IndexingStrategy\\PostgresSingleStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\PDO\\TableNameGeneratorStrategy\\Sha1' => InvokableFactory::class,
        ],
    ],
];
