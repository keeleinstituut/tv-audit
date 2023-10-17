<?php

namespace App\Listeners;

use App\Events\AuditLogEvent;
use App\Models\EventRecord;
use App\Services\AuditLogEventValidationService;
use Illuminate\Validation\ValidationException;

class AuditLogEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly AuditLogEventValidationService $validationService)
    {
    }

    /**
     * Handle the event.
     *
     * @throws ValidationException
     */
    public function handle(AuditLogEvent $amqpEvent): void
    {
        $validator = $this->validationService->makeValidator($amqpEvent->getBody());
        $validator->validate();

        EventRecord::create($validator->validated());

        $this->ackMessage($amqpEvent);
    }

    public function ackMessage(AuditLogEvent $amqpEvent): void
    {
        $amqpEvent->message->ack();
    }
}
