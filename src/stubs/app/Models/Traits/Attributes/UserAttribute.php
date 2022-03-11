<?php

namespace App\Models\Traits\Attributes;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

/**
 * Trait UserAttribute.
 */
trait UserAttribute
{
    /**
     * Is user admin?
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isAdmin(): Attribute
    {
        return new Attribute(
            get: fn () => config('backpack.permissionmanager.backpack_access_permission') && permission_exists(config('backpack.permissionmanager.backpack_access_permission')) ? $this->can(config('backpack.permissionmanager.backpack_access_permission')) : true,
        );
    }

    /**
     * Get the user's full name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => "{$attributes['first_name']} {$attributes['last_name']}",
        );
    }

    /**
     * Interact with the user's password.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function password(): Attribute
    {
        return new Attribute(
            set: fn ($value) => Hash::needsRehash($value) ? Hash::make($value) : $value,
        );
    }
}
