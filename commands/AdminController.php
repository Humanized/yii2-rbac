<?php

namespace humanized\rbac\commands;

use humanized\clihelpers\controllers\Controller;
use yii\helpers\Console;

/**
 * A CLI port of the Yii2 RBAC Manager Interface.
 * 
 * 
 * @name RBAC Manager CLI
 * @version 0.1
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 *
 */
class AdminController extends Controller {

    private $_model;
    private $_userClass;

    /**
     *
     * @var \yii\rbac\DbManager 
     */
    private $_authManager;
    private $_propertyMap = [
        'assignment' => ['assign', 'create-role', 'create-permission', 'add-role-user', 'add-role-child-role', 'add-role-child-permission'],
        'string' => ['assignment-table', 'item-child-table', 'item-table', 'rule-table']
    ];

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        $this->_userClass = $module->params['identityClass'];

        $this->_authManager = \Yii::$app->authManager;
    }

    private function findModel($user)
    {
        $userClass = $this->_userClass;
        $fn = $this->_findUser;
        return $userClass::$fn($user);
    }

    public function actionIndex()
    {
        echo "Welcome to the Yii RBAC Management CLI \n";
        echo "This tool requires Yii 2.0.7 or later \n";
        return 0;
    }

    /*
     * =========================================================================
     *                          User Linkage
     * =========================================================================
     */

    /**
     * 
     * 
     * 
     * @param string $roleName
     * @param string $userName
     * @return int exitCode - 0 default, else error code
     */
    public function actionAssign($roleName, $email)
    {
        return $this->_userFn($email, $roleName, 'getRole', 'assign');
    }

    /**
     * 
     * Revokes a role from a user
     * 
     * @param string $roleName
     * @param string $email
     * @return int exitCode - 0 default, else error code
     */
    public function actionRevoke($roleName, $email)
    {
        return $this->_userFn($email, $roleName, 'getRole', 'revoke');
    }

    /**
     * Private function used for assign and revoke actions
     * 
     * @param type $email
     * @param type $link
     * @param type $sfn
     * @param type $lfn - The function used for linking
     * @return type
     */
    private function _userFn($email, $link, $sfn, $lfn)
    {
        //Get User Model ID
        $userId = $this->_getUserId($email);
        //Exit when user not found
        if (!isset($userId)) {
            return $this->_exitCode;
        }
        //Get AuthItem (i.e. role or permission)
        $item = $this->_authManager->$sfn($link);
        //Exit when item not found
        if (!isset($item)) {
            $this->_exitCode = '300';
            $this->_msg = 'No such Role';
            return $this->_exitCode;
        }
        //Try linking function
        try {
            $this->_authManager->$lfn($item, $userId);
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }
        //Exit with try-catch results
        return $this->exitCode;
    }

    private function _getUserId($email)
    {
        $userClass = $this->_userClass;
        try {
            $user = $userClass::findOne(['email' => $email]);
            if (!isset($user)) {
                $this->_msg = 'No such user';
                $this->_exitCode = '200';

                return NULL;
            }
        } catch (\Exception $e) {
            $this->_msg = $e->getMessage();
            $this->_exitCode = $e->getCode();
            return NULL;
        }
        return $user->id;
    }

    /*
     * =========================================================================
     *                          Auth Item Creation
     * =========================================================================
     */

    /**
     * 
     * @param string $roleName
     */
    public function actionCreateRole($roleName)
    {
        return $this->createItem($roleName, 'role');
    }

    /**
     * 
     * @param string $permissionName
     */
    public function actionCreatePermission($permissionName)
    {
        return $this->createItem($permissionName, 'permission');
    }

    public function createItem($name, $type)
    {
        $fn = 'create' . (ucfirst($type));
        $item = $this->_authManager->$fn($name);
        try {
            if ($this->_authManager->add($item)) {
                $this->_msg = ucfirst($type) . ' added to system.';
            } else {
                $this->_exitCode = $type == 'role' ? '1' : '2' . '02';
                $this->_msg = 'Unhandled Error';
            }
        } catch (\Exception $e) {
            $this->_exitCode = $type == 'role' ? '1' : '2' . '01';
            $this->_msg = $e->getMessage();
        }
        return $this->_exitCode;
    }

    /*
     * =========================================================================
     *                          Hierarchical Operations
     * =========================================================================
     * 
     */

    public function actionAddChildRoleToRole($parentName, $childName)
    {
        
    }

    public function actionAddChildPermissionToPermission($parentName, $childName)
    {
        
    }

    public function actionAddChildPermissionToRole($parentName, $childName)
    {
        $this->_addChild(['from' => 'role', 'to' => 'permission', 'parent' => $parentName, 'child' => $childName]);
    }

    private function _addChild($config)
    {
        
    }

    /*
     * =========================================================================
     *                              Public Properties
     * =========================================================================
     */

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for Success, else error code
     */
    public function actionAssignmentTable()
    {
        return $this->_exitCode;
    }

    public function actionDefaultRoles()
    {
        return $this->_exitCode;
    }

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for Success, else error code
     */
    public function actionItemChildTable()
    {
        return $this->_exitCode;
    }

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for Success, else error code
     */
    public function actionItemTable()
    {
        return $this->_exitCode;
    }

    public function actionPermissions()
    {
        return $this->_exitCode;
    }

    public function actionRoles()
    {
        return $this->_exitCode;
    }

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for Success, else error code
     */
    public function actionRuleTable()
    {
        return $this->_exitCode;
    }

    /*
     * =========================================================================
     *                              Helpers
     * =========================================================================
     */

    public function afterAction($action, $result)
    {
        // echo $this->preventDefault;
        $print = NULL;
        $map = NULL;
        $this->_setupFunctions($action, $print, $map);
        $store = parent::afterAction($action, $result);

        if ($store == 0) {
            $this->$print($action->id, $map);
        }
        return $store;
    }

    private function _setupFunctions($action, &$print, &$map)
    {
        if (in_array($action->id, $this->_propertyMap['string'])) {
            $this->_setupValueFunctions($action, $print, $map);
        }
        if (in_array($action->id, $this->_propertyMap['assignment'])) {
            $this->_setupAssignmentFunction($action, $print, $map);
        }
    }

    private function _setupValueFunctions(&$print, &$map)
    {
        $map = function($x) {
            $procName = lcfirst(\yii\helpers\Inflector::id2camel($x));
            return $this->_authManager->$procName;
        };
        $print = 'printValue';
        $this->_msg = 'Printing property string to output...';
    }

    private function _setupAssignmentFunction($action, &$print, &$map)
    {
        $map = NULL;
        $print = 'printAssignment';
    }

    private function _setupValueCollectionFunctions(&$print, &$map)
    {
        
    }

}
