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

use Prooph\EventStore\Http\Api\Action;

/**
 * Expressive routed middleware
 */

/** @var \Zend\Expressive\Application $app */

// event store routes

$app->get(
    '/stream/{streamname}[/{start:head|[0-9]+}[/{direction:forward|backward}[/{count:[0-9]+}]]]',
    Action\LoadStream::class,
    'page::query-stream'
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
    'page::post-stream'
);

$app->get(
    '/streammetadata/{streamname}',
    Action\FetchStreamMetadata::class,
    'page::fetch-stream-metadata'
);

$app->post(
    '/streammetadata/{streamname}',
    [
        \Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware::class,
        Action\UpdateStreamMetadata::class,
    ],
    'page::update-stream-metadata'
);

$app->post(
    '/delete/{streamname}',
    Action\DeleteStream::class,
    'page::delete-stream'
);

$app->get(
    '/has-stream/{streamname}',
    Action\HasStream::class,
    'page::has-stream'
);

$app->get(
    '/streams[/{filter}]',
    Action\FetchStreamNames::class,
    'page::fetch-stream-names'
);

$app->get(
    '/streams-regex/{filter}',
    Action\FetchStreamNamesRegex::class,
    'page::fetch-stream-names-regex'
);

$app->get(
    '/categories[/{filter}]',
    Action\FetchCategoryNames::class,
    'page::fetch-category-names'
);

$app->get(
    '/categories-regex/{filter}',
    Action\FetchCategoryNamesRegex::class,
    'page::fetch-category-names-regex'
);

// projection manager routes

$app->get(
    '/projections/fetch-names[/{filter}]',
    Action\FetchProjectionNames::class,
    'page::fetch-projection-names'
);

$app->get(
    '/projections/fetch-names-regex/{filter}',
    Action\FetchProjectionNamesRegex::class,
    'page::fetch-projection-names-regex'
);

$app->post(
    'projection/delete/{name}/{deleteEmittedEvents:true|false}',
    Action\DeleteProjection::class,
    'page::delete-projection'
);

$app->post(
    'projection/reset/{name}',
    Action\ResetProjection::class,
    'page::reset-projection'
);

$app->post(
    'projection/stop/{name}',
    Action\StopProjection::class,
    'page::stop-projection'
);

$app->get(
    'projection/status/{name}',
    Action\FetchProjectionStatus::class,
    'page::fetch-projection-status'
);

$app->get(
    'projection/state/{name}',
    Action\FetchProjectionState::class,
    'page::fetch-projection-state'
);

$app->get(
    'projection/stream-positions/{name}',
    Action\FetchProjectionStreamPositions::class,
    'page::fetch-projection-stream-positions'
);
