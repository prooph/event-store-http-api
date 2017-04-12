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
 * Expressive middleware pipeline
 */

/** @var \Zend\Expressive\Application $app */
$app->pipe(\Zend\Stratigility\Middleware\ErrorHandler::class);
$app->pipeRoutingMiddleware();
$app->pipe(\Zend\Expressive\Helper\UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();
$app->pipe(\Zend\Expressive\Middleware\NotFoundHandler::class);
