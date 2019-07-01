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

namespace Prooph\EventStore\Http\Api\Container\Infrastructure;

use Prooph\EventStore\Http\Middleware\UrlHelper;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper as ExpressiveUrlHelper;

final class UrlHelperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $urlHelper = $container->get(ExpressiveUrlHelper::class);

        return new class($urlHelper) implements UrlHelper {
            /**
             * @var ExpressiveUrlHelper
             */
            private $expressiveUrlHelper;

            public function __construct(ExpressiveUrlHelper $urlHelper)
            {
                $this->expressiveUrlHelper = $urlHelper;
            }

            public function generate(string $urlId, array $params = []): string
            {
                return $this->expressiveUrlHelper->generate($urlId, $params);
            }
        };
    }
}
