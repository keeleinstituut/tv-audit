<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Setting;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'event_record_retention_time' => 'integer|min:60',
            'event_record_retention_time' => 'integer|min:' . Setting::MIN_EVENT_RECORD_RETENTION_TIME . '|max:' . Setting::MAX_EVENT_RECORD_RETENTION_TIME,
        ];
    }
}
