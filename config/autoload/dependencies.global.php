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

use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Container\ErrorHandlerFactory;
use Zend\Expressive\Container\ErrorResponseGeneratorFactory;
use Zend\Expressive\Container\NotFoundDelegateFactory;
use Zend\Expressive\Container\NotFoundHandlerFactory;
use Zend\Expressive\Delegate\NotFoundDelegate;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\ServerUrlMiddlewareFactory;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\OriginalMessages;

return [
    'dependencies' => [
        'aliases' => [
            MessageConverter::class => NoOpMessageConverter::class,
        ],
        'factories' => [
            // expressive
            Application::class => ApplicationFactory::class,
            ErrorHandler::class => ErrorHandlerFactory::class,
            ErrorResponseGenerator::class => ErrorResponseGeneratorFactory::class,
            ServerUrlHelper::class => InvokableFactory::class,
            ServerUrlMiddleware::class => ServerUrlMiddlewareFactory::class,
            NotFoundDelegate::class => NotFoundDelegateFactory::class,
            NotFoundHandler::class => NotFoundHandlerFactory::class,
            OriginalMessages::class => InvokableFactory::class,
            // app
            GenericEventFactory::class => InvokableFactory::class,
            // actions
            Action\Load::class => Container\Action\LoadFactory::class,
            Action\Post::class => Container\Action\PostFactory::class,
            // prooph
            FQCNMessageFactory::class => InvokableFactory::class,
            NoOpMessageConverter::class => InvokableFactory::class,
            // for pdo event-store
            'Prooph\\EventStore\\Pdo\\PersistenceStrategy\\MySqlAggregateStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Pdo\\PersistenceStrategy\\MySqlSingleStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Pdo\\PersistenceStrategy\\PostgresAggregateStreamStrategy' => InvokableFactory::class,
            'Prooph\\EventStore\\Pdo\\PersistenceStrategy\\PostgresSingleStreamStrategy' => InvokableFactory::class,
        ],
    ],
];
