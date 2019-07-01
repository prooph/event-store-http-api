<?php

/**
 * This file is part of prooph/event-store-http-api.
 * (c) 2016-2019 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2016-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Zend\ConfigAggregator\ConfigAggregator;

return [
    'debug' => false,
    ConfigAggregator::ENABLE_CACHE => true,
    'zend-expressive' => [
        'programmatic_pipeline' => true,
        'raise_throwables' => true,
    ],
];
