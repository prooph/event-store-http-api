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

/**
 * Expressive routed middleware
 */

/** @var \Zend\Expressive\Application $app */
$app->get(
    '/streams/{streamname}[/{start:head|[0-9]+}[/{direction:forward|backward}[/{count:[0-9]+}]]]',
    \Prooph\EventStore\Http\Api\Action\Load::class,
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
        \Prooph\EventStore\Http\Api\Action\Post::class,
    ],
    'page::post-stream'
);
