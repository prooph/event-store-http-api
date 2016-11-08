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

use Zend\Expressive\Router;

return [
    'dependencies' => [
        'aliases' => [
            Router\RouterInterface::class => Router\FastRouteRouter::class,
        ],
        'factories' => [
            Router\FastRouteRouter::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ]
    ],
    'routes' => [
        [
            'name' => 'query-stream',
            'path' => '/streams/{streamname}[/{start:head|[0-9]+}[/{direction:forward|backward}[/{count:[0-9]+}]]]',
            'middleware' => Action\Stream::class,
            'allowed_methods' => ['GET'],
            'options' => [
                'defaults' => [
                    'start' => 1,
                    'direction' => 'forward',
                    'count' => 10,
                ],
            ],
        ],
    ],
];
