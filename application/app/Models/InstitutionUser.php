<?php

namespace App\Models;

use App\Traits\HasReadonlyAccess;
use Illuminate\Database\Eloquent\Model;

class InstitutionUser extends Model
{
    // use HasReadonlyAccess;

    protected $table = 'cached_institution_users';
}
