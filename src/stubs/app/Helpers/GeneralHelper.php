<?php

use Spatie\Permission\Models\Permission;
// use Carbon\Carbon;

if (! function_exists('as_array')) {
    function as_array(mixed $value)
    {
        if (!$value)
        {
            return [];
        }

        if (!is_array($value))
        {
            return array($value);
        }

        return $value;
    }
}

if (! function_exists('role_exists')) {
    /**
     * Check if role exists
     *
     * @param string $role
     * @return bool
     *
     * @throws Exception
     */
    function role_exists($role)
    {
        try
        {
            Spatie\Permission\Models\Role::findByName($role)->get();
        }
        catch(Spatie\Permission\Exceptions\RoleDoesNotExist $e)
        {
            return false;
        }

        return true;
    }
}

if (! function_exists('permission_exists')) {
    /**
     * Check if permission exists
     *
     * @param string $role
     * @return bool
     *
     * @throws Exception
     */
    function permission_exists($permission)
    {
        try
        {
            Spatie\Permission\Models\Permission::findByName($permission)->get();
        }
        catch(Spatie\Permission\Exceptions\PermissionDoesNotExist $e)
        {
            return false;
        }

        return true;
    }
}

// if (! function_exists('carbon')) {
//     /**
//      * Create a new Carbon instance from a time.
//      *
//      * @param $time
//      * @return Carbon
//      *
//      * @throws Exception
//      */
//     function carbon($time)
//     {
//         return new Carbon($time);
//     }
// }
