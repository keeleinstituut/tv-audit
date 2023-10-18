<?php

namespace App\Events;

use PhpAmqpLib\Message\AMQPMessage;
use SyncTools\Events\BaseConsumedEvent;

class IncomingAuditLogMessageEvent extends BaseConsumedEvent
{
    public function __construct(public readonly AMQPMessage $message)
    {
    }

    public static function produceFromMessage(AMQPMessage $message): static
    {
        return new static($message);
    }

    public function getBody(): array
    {
        return json_decode($this->message->getBody(), true);
    }
}
