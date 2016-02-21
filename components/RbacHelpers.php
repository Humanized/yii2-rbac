<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humanized\rbac\components;

class RbacHelpers
{

    public static function buildLinearRoleHierarchy($roleNames)
    {

        $parent = NULL;
        foreach ($roleNames as $roleName) {
            $child = \Yii::$app->authManager->createRole($roleName);
            \Yii::$app->authManager->add($child);
            if (isset($parent)) {
                \Yii::$app->authManager->addChild($parent, $child);
            }
            $parent = $child;
        }
    }

    public static function createRolePermissions($roleName, $permissionNames)
    {
        $role = \Yii::$app->authManager->getRole($roleName);
        foreach ($permissionNames as $permissionName) {
            $permission = \Yii::$app->authManager->createPermission($permissionName);
            \Yii::$app->authManager->add($permission);
            \Yii::$app->authManager->addChild($role, $permission);
        }
    }

}
