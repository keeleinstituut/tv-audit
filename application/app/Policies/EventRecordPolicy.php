<?php

namespace App\Policies;

use App\Enums\PrivilegeKey;
use App\Models\EventRecord;
use BadMethodCallException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use KeycloakAuthGuard\Middleware\EnsureJwtBelongsToServiceAccountWithSyncRole;

readonly class EventRecordPolicy
{
    public function __construct(private EnsureJwtBelongsToServiceAccountWithSyncRole $syncRoleAuthChecker)
    {
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(mixed $user): bool
    {
        return Auth::hasPrivilege(PrivilegeKey::ViewAuditLog->value);
    }

    /**
     * Determine whether the user can export audit logs.
     */
    public function export(mixed $user): bool
    {
        return Auth::hasPrivilege(PrivilegeKey::ExportAuditLog->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(mixed $user, string $jwt): bool
    {
        return $this->syncRoleAuthChecker->jwtHasRealmRole($jwt, Config::get('amqp.audit_logs.required_jwt_realm_role'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(mixed $user, EventRecord $event): bool
    {
        throw new BadMethodCallException();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(mixed $user, EventRecord $event): bool
    {
        throw new BadMethodCallException();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(mixed $user, EventRecord $event): bool
    {
        throw new BadMethodCallException();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(mixed $user, EventRecord $event): bool
    {
        throw new BadMethodCallException();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(mixed $user, EventRecord $event): bool
    {
        throw new BadMethodCallException();
    }

    // Should serve as an query enhancement to Eloquent queries
    // to filter out objects that the user does not have permissions to.
    //
    // Example usage in query:
    // Role::getModel()->withGlobalScope('policy', RolePolicy::scope())->get();
    //
    // The 'policy' string in the example is not strict and is used internally to identify
    // the scope applied in Eloquent querybuilder. It can be something else as well,
    // but it should correspond with the intentions of the scope. Using 'policy' provides
    // general understanding throughout the whole project that the applied scope is related to policy.
    // The withGlobalScope method does not apply the scope globally, it applies to only the querybuilder
    // of current query. The method name could be different, but in the sake of reusability
    // we can use this method that's provided by Laravel and used internally.
    //
    public static function scope()
    {
        return new Scope\EventScope();
    }
}

// Scope resides in the same file with Policy to enforce scope creation with policy creation.

namespace App\Policies\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope as IScope;
use Illuminate\Support\Facades\Auth;

class EventScope implements IScope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('context_institution_id', Auth::user()->institutionId);
    }
}
