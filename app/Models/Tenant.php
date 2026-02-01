<?php

namespace App\Models;

use App\Observers\TenantObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([TenantObserver::class])]
class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'active',
        'slug',
    ];


    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
