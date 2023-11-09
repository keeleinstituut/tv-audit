<?php

namespace App\Models;

use AuditLogClient\Enums\AuditLogEventFailureType;
use AuditLogClient\Enums\AuditLogEventType;
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
 * @property int|null $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Carbon|null $happened_at
 * @property string|null $trace_id
 * @property string|null $acting_institution_user_id
 * @property string|null $context_institution_id
 * @property string|null $context_department_id
 * @property string|null $acting_user_pic
 * @property array|null $event_parameters
 * @property AuditLogEventType $event_type
 * @property AuditLogEventFailureType|null $failure_type If not null, then the attempted action failed. The reason of failure is described by this column.
 * @property string|null $acting_user_forename
 * @property string|null $acting_user_surname
 *
 * @method static Builder|EventRecord newModelQuery()
 * @method static Builder|EventRecord newQuery()
 * @method static Builder|EventRecord query()
 * @method static Builder|EventRecord whereActingInstitutionUserId($value)
 * @method static Builder|EventRecord whereCreatedAt($value)
 * @method static Builder|EventRecord whereDeletedAt($value)
 * @method static Builder|EventRecord whereEventParameters($value)
 * @method static Builder|EventRecord whereEventType($value)
 * @method static Builder|EventRecord whereFailureType($value)
 * @method static Builder|EventRecord whereHappenedAt($value)
 * @method static Builder|EventRecord whereId($value)
 * @method static Builder|EventRecord whereTraceId($value)
 * @method static Builder|EventRecord whereUpdatedAt($value)
 * @method static Builder|EventRecord onlyTrashed()
 * @method static Builder|EventRecord withTrashed()
 * @method static Builder|EventRecord withoutTrashed()
 * @method static Builder|EventRecord whereActingUserPic($value)
 * @method static Builder|EventRecord whereContextDepartmentId($value)
 * @method static Builder|EventRecord whereContextInstitutionId($value)
 * @method static Builder|EventRecord whereActingUserForename($value)
 * @method static Builder|EventRecord whereActingUserSurname($value)
 *
 * @mixin Eloquent
 */
class EventRecord extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'event_records';

    protected $guarded = [];

    protected $casts = [
        'event_type' => AuditLogEventType::class,
        'failure_type' => AuditLogEventFailureType::class,
        'event_parameters' => 'array',
        'happened_at' => 'datetime',
    ];
}
