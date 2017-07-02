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

namespace ProophTest\EventStore\Http\Api\Integration;

use PHPUnit\Framework\TestCase;
use ProophTest\EventStore\Pdo\TestUtil;

abstract class AbstractHttpApiServerTestCase extends TestCase
{
    /**
     * @var int
     */
    protected $projectionPid;

    protected function setUp(): void
    {
        copy(__DIR__ . '/event_store.local.php', __DIR__ . '/../../config/autoload/event_store.local.php');

        $connection = TestUtil::getConnection();
        $database = TestUtil::getDatabaseName();

        $connection->exec('DROP DATABASE ' . $database);
        $connection->exec('CREATE DATABASE ' . $database);

        TestUtil::initDefaultDatabaseTables($connection);

        $path = __DIR__ . '/../../public';

        $command = 'exec php -S 0.0.0.0:8080 -t ' . $path;

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        $processDetails = proc_get_status($process);

        $this->projectionPid = $processDetails['pid'];

        sleep(1);
    }

    protected function tearDown(): void
    {
        posix_kill($this->projectionPid, SIGKILL);
        usleep(500000);
    }
}
