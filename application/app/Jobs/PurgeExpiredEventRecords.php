<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\Setting;
use App\Models\EventRecord;

/**
 * Job that is reponsible for iterating over all institutions and
 * purging EventRecord objects that have passed it's retention time
 * as set in Settings object for each institution.
 */
class PurgeExpiredEventRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting deletion of expired EventRecords");

        $settings = Setting::all();

        $settings->each(function ($setting) {
            Log::info("Purging records for Institution with ID $setting->institution_id");
            
            Log::info($setting->event_record_retention_time);
            Log::info(Carbon::now());
            Log::info("Deleting records older than " . $setting->getEventRecordExpiryDateTime());

            $eventRecordsQuery = EventRecord::getModel()
                ->where('context_institution_id', $setting->institution_id);

            $expiredEventRecordsQuery = $eventRecordsQuery->clone()
                ->where('happened_at', '<', $setting->getEventRecordExpiryDateTime());

            Log::info("Count of all event records: " . $eventRecordsQuery->count());
            Log::info("Count of expired event records: " . $expiredEventRecordsQuery->count());

            $deletedCount = $expiredEventRecordsQuery->clone()->delete();
            Log::info("Deleted $deletedCount event records");
        });

       $forceDeleted = EventRecord::getModel()->onlyTrashed()->forceDelete();
       Log::info("Total deleted count of event records: " . $forceDeleted);
    }
}
