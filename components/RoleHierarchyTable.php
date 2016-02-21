<?php

namespace humanized\rbac\components;

use humanized\rbac\components\RbacHelpers;

/**
 * Role Hierarchy Table
 * Data loader for simple linear rbac hierarchies
 */
class RoleHierarchyTable extends \humanized\clihelpers\components\DataTable
{

    public $modelClass = NULL;

    /**
     *
     * @var array[role-name=>['permissions'=>'']] 
     */
    public $data = [];

    public static function load()
    {

        $class = get_called_class();
        echo 'Loading role hierarchy from file: ' . "$class... ";
        $instance = new $class();
        var_dump(array_keys($instance->data));
        RbacHelpers::buildLinearRoleHierarchy(array_keys($instance->data));
        foreach ($instance->data as $roleName => $permissionNames) {

            RbacHelpers::createRolePermissions($roleName, $permissionNames);
        }
        echo 'Done' . "\n";
    }

}
