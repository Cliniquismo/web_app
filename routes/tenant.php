<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Route;

Route::view('tenants/create', 'tenants.create')
    ->middleware(['auth', 'verified', 'has.tenant'])
    ->name('tenants.create');

Route::get('tenants/{tenant}/edit', function (Tenant $tenant) {
    return view('tenants.edit', compact('tenant'));
})->middleware(['auth', 'verified'])
    ->name('tenants.edit');
