<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Setting extends Model
{
    use HasUuids;
    use HasFactory;

    public const MINIMUM_EVENT_RECORD_RETENTION_TIME = 365 * 2; // 2 years in days

    protected $fillable = [
        'institution_id',
        'event_record_retention_time',
    ];

    // Default values
    protected $attributes = [
        'event_record_retention_time' => Setting::MINIMUM_EVENT_RECORD_RETENTION_TIME,
    ];

    public function getEventRecordExpiryDateTime() {
        $now = Carbon::now();
        return $now->subDays($this->event_record_retention_time);
    }

    // Tries to find existing Setting for specific institution from database.
    // If missing then returns new instance of Setting with default values.
    public static function getForInstitution(string $institutionId) {
        return Setting::firstOrNew([
            'institution_id' => $institutionId,
        ]);
    }
}
