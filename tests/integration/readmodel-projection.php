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

namespace ProophTest\EventStore\Http\Api\Integration;

require __DIR__ . '/../../vendor/autoload.php';

use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\GenericEventFactory;
use Prooph\EventStore\Pdo\Container\PdoConnectionFactory;
use Prooph\EventStore\Pdo\Container\PostgresEventStoreFactory;
use Prooph\EventStore\Pdo\Container\PostgresProjectionManagerFactory;
use Prooph\EventStore\Pdo\PersistenceStrategy\PostgresSimpleStreamStrategy;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\ReadModelProjector;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

$config = [
    'dependencies' => [
        'factories' => [
            EventStore::class => PostgresEventStoreFactory::class,
            ProjectionManager::class => PostgresProjectionManagerFactory::class,
            'pdo_connection' => PdoConnectionFactory::class,
            PostgresSimpleStreamStrategy::class => InvokableFactory::class,
            GenericEventFactory::class => InvokableFactory::class,
            FQCNMessageFactory::class => GenericEventFactory::class,
        ],
    ],
    'prooph' => [
        'event_store' => [
            'default' => [
                'connection' => 'pdo_connection',
                'message_factory' => GenericEventFactory::class,
                'persistence_strategy' => PostgresSimpleStreamStrategy::class,
            ],
        ],
        'pdo_connection' => [
            'default' => [
                'schema' => substr(getenv('DB_DRIVER'), 4),
                'user' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'host' => getenv('DB_HOST'),
                'dbname' => getenv('DB_NAME'),
                'port' => getenv('DB_PORT'),
                'charset' => getenv('DB_CHARSET'),
            ],
        ],
        'projection_manager' => [
            'default' => [
                'connection' => 'pdo_connection',
            ],
        ],
    ],
];

$sm = new ServiceManager($config['dependencies']);
$sm->setService('config', $config);

$projectionManager = $sm->get(ProjectionManager::class);

$readModel = new class() implements ReadModel {
    public function init(): void
    {
    }

    public function isInitialized(): bool
    {
        return true;
    }

    public function reset(): void
    {
    }

    public function delete(): void
    {
    }

    public function stack(string $operation, ...$args): void
    {
    }

    public function persist(): void
    {
    }
};

$projection = $projectionManager->createReadModelProjection('test-readmodel-projection', $readModel, [
    ReadModelProjector::OPTION_PCNTL_DISPATCH => true,
]);

$projection
    ->init(function (): array {
        return ['counter' => 0];
    })
    ->fromAll()
    ->whenAny(function (array $state, Message $message): array {
        $state['counter']++;

        return $state;
    })
    ->run(true);
