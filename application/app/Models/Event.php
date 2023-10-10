<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $happened_at
 * @property string $event_type
 * @property string|null $trace_id
 * @property string|null $institution_id
 * @property string|null $acting_institution_user_id
 * @property string|null $target_object_type
 * @property mixed|null $event_parameters
 * @property mixed|null $target_object_before_event
 * @property mixed|null $target_object_after_event
 * @property string|null $failure_type
 *
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereActingInstitutionUserId($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereDeletedAt($value)
 * @method static Builder|Event whereEventParameters($value)
 * @method static Builder|Event whereEventType($value)
 * @method static Builder|Event whereFailureType($value)
 * @method static Builder|Event whereHappenedAt($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereInstitutionId($value)
 * @method static Builder|Event whereTargetObjectAfterEvent($value)
 * @method static Builder|Event whereTargetObjectBeforeEvent($value)
 * @method static Builder|Event whereTargetObjectType($value)
 * @method static Builder|Event whereTraceId($value)
 * @method static Builder|Event whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Event extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'events';
}
