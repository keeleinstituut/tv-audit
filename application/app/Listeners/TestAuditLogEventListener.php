<?php

namespace App\Listeners;

use DB;
use App\Events\TestAuditLogEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use App\Models\EventRecord2;

class TestAuditLogEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TestAuditLogEvent $event): void
    {
        dump($event->getBody());


        DB::transaction(function () use ($event) {
            $body = $event->getBody();

            $data = [
                'happened_at' => data_get($body, 'general.happened_at'),

                'actor_pic' => data_get($body, 'general.actor_pic'),
                'actor_name' => data_get($body, 'general.actor_name'),
                'actor_session' => data_get($body, 'general.actor_session'),
                'actor_department_id' => data_get($body, 'general.actor_department_id'),
                'actor_institution_id' => data_get($body, 'general.actor_institution_id'),
                'actor_institution_user_id' => data_get($body, 'general.actor_institution_user_id'),

                'action' => data_get($body, 'general.action'),
                'web_path' => data_get($body, 'general.web_path'),

                'path' => data_get($body, 'request.path'),
                'request_method' => data_get($body, 'request.method'),
                'request_query' => data_get($body, 'request.query'),
                'request_body' => data_get($body, 'request.body'),

                // 'response_content_type' => data_get($body, 'response.headers.content-type'),
                // 'response_body' => data_get($body, 'response.body'),
                'response_status_code' => data_get($body, 'response.status_code'),
            ];

            // if ($data['response_content_type'] == 'application/json') {
            //     $data['response_body'] = json_decode($data['response_body'], true);
            // }

            $validator = Validator::make($data, [
                'happened_at' => 'required|date',

                'actor_pic' => 'required|string',
                'actor_name' => 'required|string',
                'actor_session' => 'required|string',
                'actor_department_id' => 'required|string',
                'actor_institution_id' => 'required|string',
                'actor_institution_user_id' => 'required|string',

                'action' => 'required|string',
                'web_path' => 'required|string',

                'path' => 'required|string',
                'request_method' => 'required|string',
                'request_query' => 'nullable|array',
                'request_body' => 'nullable|array',

                'response_status_code' => 'required|integer',
            ]);

            $record = new EventRecord2();
            $record->fill($validator->validated());
            $record->save();

            $event->message->ack();
        });

    }
}
