<?php

namespace App\Http\Requests;

use AuditLogClient\Enums\AuditLogEventType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchAuditLogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_datetime' => 'date',
            'end_datetime' => 'date',
            'department_id' => 'uuid',
            'acting_user_pic' => 'string',
            'event_type' => Rule::enum(AuditLogEventType::class),
            'text' => 'string',
        ];
    }
}
