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

use Prooph\EventStore\Http\Middleware\Action;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Expressive routed middleware
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get(
        '/stream/{streamname}[/{start:head|[0-9]+}[/{direction:forward|backward}[/{count:[0-9]+}]]]',
        Action\LoadStream::class,
        'EventStore::load'
    )
        ->setOptions([
                'defaults' => [
                    'start' => 1,
                    'direction' => 'forward',
                    'count' => 10,
                ],
            ]
        );

    $app->post(
        '/stream/{streamname}',
        [
            \Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware::class,
            Action\PostStream::class,
        ],
        'EventStore::appendTo'
    );

    $app->get(
        '/streammetadata/{streamname}',
        Action\FetchStreamMetadata::class,
        'EventStore::fetchStreamMetadata'
    );

    $app->post(
        '/streammetadata/{streamname}',
        [
            \Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware::class,
            Action\UpdateStreamMetadata::class,
        ],
        'EventStore::updateStreamMetadata'
    );

    $app->post(
        '/delete/{streamname}',
        Action\DeleteStream::class,
        'EventStore::delete'
    );

    $app->get(
        '/has-stream/{streamname}',
        Action\HasStream::class,
        'EventStore::hasStream'
    );

    $app->get(
        '/streams[/{filter}]',
        Action\FetchStreamNames::class,
        'EventStore::fetchStreamNames'
    );

    $app->get(
        '/streams-regex/{filter}',
        Action\FetchStreamNamesRegex::class,
        'EventStore::fetchStreamNamesRegex'
    );

    $app->get(
        '/categories[/{filter}]',
        Action\FetchCategoryNames::class,
        'EventStore::fetchCategoryNames'
    );

    $app->get(
        '/categories-regex/{filter}',
        Action\FetchCategoryNamesRegex::class,
        'EventStore::fetchCategoryNamesRegex'
    );

    // projection manager routes

    $app->get(
        '/projections[/{filter}]',
        Action\FetchProjectionNames::class,
        'ProjectionManager::fetchProjectionNames'
    );

    $app->get(
        '/projections-regex/{filter}',
        Action\FetchProjectionNamesRegex::class,
        'ProjectionManager::fetchProjectionNamesRegex'
    );

    $app->post(
        '/projection/delete/{name}/{deleteEmittedEvents:true|false}',
        Action\DeleteProjection::class,
        'ProjectionManager::deleteProjection'
    );

    $app->post(
        '/projection/reset/{name}',
        Action\ResetProjection::class,
        'ProjectionManager::resetProjection'
    );

    $app->post(
        '/projection/stop/{name}',
        Action\StopProjection::class,
        'ProjectionManager::stopProjection'
    );

    $app->get(
        '/projection/status/{name}',
        Action\FetchProjectionStatus::class,
        'ProjectionManager::fetchProjectionStatus'
    );

    $app->get(
        '/projection/state/{name}',
        Action\FetchProjectionState::class,
        'ProjectionManager::fetchProjectionState'
    );

    $app->get(
        '/projection/stream-positions/{name}',
        Action\FetchProjectionStreamPositions::class,
        'ProjectionManager::fetchProjectionStreamPositions'
    );
};
