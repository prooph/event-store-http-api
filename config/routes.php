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
$app->get(
    '/streams/{streamname}[/{start:head|[0-9]+}[/{direction:forward|backward}[/{count:[0-9]+}]]]',
    Action\Load::class,
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
    '/streams/{streamname}',
    [
        \Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware::class,
        Action\Post::class,
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

$app->get(
    '/delete/{streamname}',
    Action\Delete::class,
    'page::delete-stream'
);

$app->get(
    '/has-stream/{streamname}',
    Action\HasStream::class,
    'page::has-stream'
);

$app->get(
    '/projections/fetch-names[/{filter}[/limit:[0-9]+][/offset:[0-9]+]]',
    Action\FetchProjetionNames::class,
    'page::fetch-projection-names'
)
    ->setOptions([
        'defaults' => [
            'limit' => 20,
            'offet' => 0,
        ],
    ]
);
