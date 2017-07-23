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

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client;
use PHPUnit\Framework\TestCase;
use ProophTest\EventStore\Pdo\TestUtil;

abstract class AbstractHttpApiServerTestCase extends TestCase
{
    /**
     * @var int
     */
    protected $serverPid;

    /**
     * @var int|null
     */
    protected $projectionPid;

    /**
     * @var int|null
     */
    protected $readModelProjectionPid;

    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @var Client
     */
    protected $client;

    protected function setUp(): void
    {
        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('pcntl extension missing');
        }

        if (file_exists(__DIR__ . '/../../config/autoload/event_store.local.php')) {
            copy(__DIR__ . '/../../config/autoload/event_store.local.php', __DIR__ . '/../../config/autoload/event_store.local.php.copy');
        }

        if (file_exists(__DIR__ . '/../../config/pipeline.php')) {
            copy(__DIR__ . '/../../config/pipeline.php', __DIR__ . '/../../config/pipeline.php.copy');
        }

        copy(__DIR__ . '/../../config/pipeline.php.dist', __DIR__ . '/../../config/pipeline.php');

        $config = include __DIR__ . '/event_store.local.php';
        $configFile = '<?php return ' . var_export($config, true) . ';';
        // add server config
        file_put_contents(__DIR__ . '/../../config/autoload/event_store.local.php', $configFile);

        $path = __DIR__ . '/../../public';

        $command = 'exec php -S 0.0.0.0:8080 -t ' . $path;

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        $processDetails = proc_get_status($process);

        $this->serverPid = $processDetails['pid'];

        $this->connection = TestUtil::getConnection();

        TestUtil::initDefaultDatabaseTables($this->connection);

        $this->client = new Client();

        $failed = 0;
        $attempts = 0;

        do {
            $resource = @fsockopen('localhost', 8080, $failed);

            if (is_resource($resource)) {
                break;
            }

            if (10 === $attempts) {
                proc_terminate($process);
                $this->markTestSkipped('The stub server did not load in time (500ms).');
            }

            $attempts++;
            usleep(50000);
        } while (true);
    }

    protected function tearDown(): void
    {
        if ($this->projectionPid) {
            posix_kill($this->projectionPid, SIGKILL);
            $this->projectionPid = null;
        }

        if ($this->readModelProjectionPid) {
            posix_kill($this->readModelProjectionPid, SIGKILL);
            $this->readModelProjectionPid = null;
        }

        posix_kill($this->serverPid, SIGTERM);

        // remove server config
        unlink(__DIR__ . '/../../config/autoload/event_store.local.php');
        unlink(__DIR__ . '/../../config/pipeline.php');

        if (file_exists(__DIR__ . '/../../config/autoload/event_store.local.php.copy')) {
            copy(__DIR__ . '/../../config/autoload/event_store.local.php.copy', __DIR__ . '/../../config/autoload/event_store.local.php');
            unlink(__DIR__ . '/../../config/autoload/event_store.local.php.copy');
        }

        if (file_exists(__DIR__ . '/../../config/pipeline.php.copy')) {
            copy(__DIR__ . '/../../config/pipeline.php.copy', __DIR__ . '/../../config/pipeline.php');
            unlink(__DIR__ . '/../../config/pipeline.php.copy');
        }

        // drop event streams table
        $statement = $this->connection->prepare('DROP TABLE event_streams;');
        $statement->execute();

        // drop projections table
        $statement = $this->connection->prepare('DROP TABLE projections;');
        $statement->execute();

        // drop teststream table
        $statement = $this->connection->prepare('DROP TABLE IF EXISTS _eeaa111d0c71f10112decea3f1330e9853abe6e3;');
        $statement->execute();

        $failed = 0;
        $attempts = 0;

        do {
            $resource = @fsockopen('localhost', 8080, $failed);

            if (false === $resource) {
                break;
            }

            if (10 === $attempts) {
                $this->fail('The stub server did not shut down in time (500ms).');
            }

            $attempts++;
            usleep(50000);
        } while (true);
    }

    protected function createTestStream(): void
    {
        $request = new Request(
            'POST',
            'http://localhost:8080/stream/teststream',
            [
                'Content-Type' => 'application/vnd.eventstore.atom+json',
            ],
            '[
              {
                "uuid": "f9fea0b9-bbab-41ad-b3c1-56e09a1044a4",
                "created_at": "2016-11-12T14:35:41.702700",
                "message_name": "event-type",
                "payload": {
                  "a": "2"
                },
                "metadata":{"_aggregate_version":1}
              },
              {
                "message_name":"foo",
                "payload":{"b" : "c"},
                "metadata":{"_aggregate_version":2}
              },
              {
                "uuid": "8571b393-a4d6-4c82-be96-4371a2795d18",
                "created_at": "2016-11-12T14:35:41.705700",
                "message_name": "event-type"
              }
            ]'
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(204, $response->getStatusCode());
    }

    protected function createProjection(): void
    {
        $command = 'exec php ' . __DIR__ . '/projection.php';

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        $processDetails = proc_get_status($process);

        $this->projectionPid = $processDetails['pid'];
    }

    protected function createReadModelProjection(): void
    {
        $command = 'exec php ' . __DIR__ . '/readmodel-projection.php';

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['file', '/tmp/aa', 'w'],
            2 => ['pipe', '/tmp/ab', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        $processDetails = proc_get_status($process);

        $this->readModelProjectionPid = $processDetails['pid'];
    }

    protected function waitForProjectionsToStart(): void
    {
        usleep(200000);
    }
}
