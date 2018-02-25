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

namespace Prooph\EventStore\Http\Api\Container\Middleware;

use Prooph\EventStore\Http\Api\Middleware\BaseUrl;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

final class BaseUrlFactory
{
    public function __invoke(ContainerInterface $container): BaseUrl
    {
        $urlHelper = $container->get(UrlHelper::class);

        $config = $container->get('config');

        $baseUrl = $config['http']['base_url'] ?? '/';

        return new BaseUrl($baseUrl, $urlHelper);
    }
}
