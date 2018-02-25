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

namespace Prooph\EventStore\Http\Api\Container\Action;

use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\LoadStream;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

final class LoadStreamFactory
{
    public function __invoke(ContainerInterface $container): LoadStream
    {
        $actionHandler = new LoadStream(
            $container->get(EventStore::class),
            $container->get(MessageConverter::class),
            $container->get(UrlHelper::class)
        );

        $actionHandler->addTransformer(
            new JsonTransformer(),
            'application/vnd.eventstore.atom+json',
            'application/atom+json',
            'application/json'
        );

        return $actionHandler;
    }
}
