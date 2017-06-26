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
use Prooph\EventStore\Http\Api\Container\Middleware\BaseUrlFactory;
use Prooph\EventStore\Http\Api\Middleware\BaseUrl;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Container\ErrorHandlerFactory;
use Zend\Expressive\Container\ErrorResponseGeneratorFactory;
use Zend\Expressive\Container\NotFoundDelegateFactory;
use Zend\Expressive\Container\NotFoundHandlerFactory;
use Zend\Expressive\Delegate\NotFoundDelegate;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Helper\UrlHelperFactory;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddlewareFactory;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\Expressive\Router\FastRouteRouterFactory;
use Zend\Expressive\Router\RouterInterface;
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
            UrlHelper::class => UrlHelperFactory::class,
            UrlHelperMiddleware::class => UrlHelperMiddlewareFactory::class,
            NotFoundDelegate::class => NotFoundDelegateFactory::class,
            NotFoundHandler::class => NotFoundHandlerFactory::class,
            OriginalMessages::class => InvokableFactory::class,
            RouterInterface::class => FastRouteRouterFactory::class,
            BaseUrl::class => BaseUrlFactory::class,
            // app
            GenericEventFactory::class => InvokableFactory::class,
            // actions
            Action\DeleteStream::class => Container\Action\DeleteStreamFactory::class,
            Action\FetchStreamMetadata::class => Container\Action\FetchStreamMetadataFactory::class,
            Action\HasStream::class => Container\Action\HasStreamFactory::class,
            Action\LoadStream::class => Container\Action\LoadStreamFactory::class,
            Action\PostStream::class => Container\Action\PostStreamFactory::class,
            Action\UpdateStreamMetadata::class => Container\Action\UpdateStreamMetadataFactory::class,
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
