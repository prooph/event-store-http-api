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

use Zend\Expressive\Application;

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

error_reporting(E_ALL);

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';

try {

    /** @var \Zend\Expressive\Application $app */
    $app = $container->get(Application::class);
    $app->run();
} catch (\Throwable $e) {
    var_dump($e->getMessage());
    while ($e = $e->getPrevious()) {
        var_dump($e->getMessage());
    }
}