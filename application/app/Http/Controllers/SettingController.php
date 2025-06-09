<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Requests\SettingUpdateRequest;
use App\Http\Resources\SettingResource;

class SettingController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $institutionId)
    {
        $obj = Setting::firstOrNew([
            'institution_id' => $institutionId,
        ]);

        $this->authorize('view', $obj);


        return new SettingResource($obj);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingUpdateRequest $request, string $institutionId)
    {
        $params = collect($request->validated());

        return DB::transaction(function () use ($params, $institutionId) {
            $obj = Setting::firstOrNew([
                'institution_id' => $institutionId,
            ]);

            $this->authorize('update', $obj);

            $obj->fill($params->toArray());
            $obj->save();

            return $obj;
        });
    }

    public static function getBaseQuery(): Builder
    {
        return EventRecord::getModel()->withGlobalScope('policy', EventRecordPolicy::scope());
    }
}
