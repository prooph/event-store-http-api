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

namespace Prooph\EventStore\Http\Api;

use DateTimeImmutable;
use DateTimeZone;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;
use Ramsey\Uuid\Uuid;

class GenericEventFactory implements MessageFactory
{
    public function createMessageFromArray(string $messageName, array $messageData): Message
    {
        if (! isset($messageData['message_name'])) {
            $messageData['message_name'] = $messageName;
        }

        if (! isset($messageData['uuid'])) {
            $messageData['uuid'] = Uuid::uuid4();
        }

        if (! isset($messageData['version'])) {
            $messageData['version'] = 0;
        }

        if (! isset($messageData['created_at'])) {
            $messageData['created_at'] = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }

        if (! isset($messageData['metadata'])) {
            $messageData['metadata'] = [];
        }

        return GenericEvent::fromArray($messageData);
    }
}
