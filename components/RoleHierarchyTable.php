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
        echo 'Loading role hierarchy from file: ' . "$class \n";
        $instance = new $class();

        RbacHelpers::buildLinearRoleHierarchy(array_keys($this->data));
        foreach ($instance->data as $record) {
            RbacHelpers::createRolePermissions($record);
        }
        echo 'Complete';
    }

}
