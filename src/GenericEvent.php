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

use DateTimeImmutable;
use Prooph\Common\Messaging\Message;
use Ramsey\Uuid\Uuid;

final class GenericEvent implements Message
{
    private $data;

    public function __construct(array $messageData)
    {
        $this->data = $messageData;
    }

    public function payload(): ?array
    {
        return $this->data['payload'] ?? null;
    }

    public function messageName(): string
    {
        return $this->data['message_name'];
    }

    public function messageType(): string
    {
        return Message::TYPE_EVENT;
    }

    public function uuid(): Uuid
    {
        return $this->data['uuid'];
    }

    public function version(): int
    {
        return $this->data['version'];
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->data['created_at'];
    }

    public function metadata(): array
    {
        return $this->data['metadata'];
    }

    public function withVersion(int $version): Message
    {
        $data = $this->data;
        $data['version'] = $version;

        return new self($data);
    }

    public function withMetadata(array $metadata): Message
    {
        $data = $this->data;
        $data['metadata'] = $metadata;

        return new self($data);
    }

    public function withAddedMetadata(string $key, $value): Message
    {
        $data = $this->data;
        $data['metadata'][$key] = $value;

        return new self($data);
    }
}
