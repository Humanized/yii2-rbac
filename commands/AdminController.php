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
        'assignment' => ['assign', 'create-role', 'create-permission', 'remove-role', 'remove-permission', 'add-role-user', 'add-role-child-role', 'add-role-child-permission'],
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
     *                Operations dealing with User-Role Linkage
     * =========================================================================
     */

    /**
     * 
     * Assigns a role to a user
     * 
     * @param string $role  Named role
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code
     */
    public function actionAssign($role, $email)
    {
        return $this->_fnUser($email, 'assign', $role);
    }

    /**
     * Revokes a role from a user
     * 
     * @param string $role  Named role
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code
     */
    public function actionRevoke($role, $email)
    {
        return $this->_fnUser($email, 'revoke', $role);
    }

    /**
     * Revokes all roles from a user
     * 
     * @param type $email
     * @return type
     */
    public function actionRevokeAll($email)
    {
        return $this->_fnUser($email, 'revokeAll');
    }

    /*
     * =========================================================================
     *              Operations performed on Single Auth Item
     * =========================================================================
     */

    /**
     * 
     * @param string $name
     */
    public function actionCreateRole($name)
    {
        return $this->_fnItem(['type' => 'role', 'fn' => 'add', 'item' => $name, 'new' => TRUE]);
    }

    /**
     * 
     * @param string $name
     */
    public function actionCreatePermission($name)
    {
        return $this->_fnItem(['type' => 'permission', 'fn' => 'add', 'item' => $name, 'new' => TRUE]);
    }

    /**
     * 
     * @param string $name
     */
    public function actionRemoveRole($name)
    {
        return $this->_fnItem(['type' => 'role', 'fn' => 'remove', 'item' => $name]);
    }

    /**
     * 
     * @param string $name
     */
    public function actionRemovePermission($name)
    {
        return $this->_fnItem(['type' => 'permission', 'fn' => 'remove', 'item' => $name]);
    }

    /*
     * =========================================================================
     *                          Hierarchical Operations
     * =========================================================================
     * 
     */

    /**
     * 
     * Adds a child role to a parent role 
     * 
     * @param string $parent The parent named role
     * @param string $child The child named role
     * @return type
     */
    public function actionAddChildRole($parent, $child)
    {
        return $this->_fnChild(['proc' => 'addChild', 'from' => 'role', 'to' => 'role', 'parent' => $parent, 'child' => $child]);
    }

    /**
     *
     * Adds a child permission to a parent role-or-permission
     * If the parent is a role or permission is set by parameter, by default this method links to parent roles
     *  
     * @param type $parent The parent named role
     * @param type $child The child named role
     * @param type $alt Set to TRUE to link to a parent permission, else to a parent role (default FALSE)
     * @return type
     */
    public function actionAddChildPermission($parent, $child, $alt = FALSE)
    {
        return $this->_fnChild(['proc' => 'addChild', 'from' => $alt == FALSE ? 'role' : 'permisison', 'to' => 'permission', 'parent' => $parent, 'child' => $child]);
    }

    /**
     * 
     * @param type $parent
     * @param type $child
     * @return type
     */
    public function actionRemoveChildRole($parent, $child)
    {

        return $this->_fnChild(['proc' => 'removeChild', 'from' => 'role', 'to' => 'role', 'parent' => $parent, 'child' => $child]);
    }

    /**
     * Removes a child permission from a parent role-or-permission
     * If the parent is a role or permission is set by parameter, by default this method links to parent roles
     *  
     * @param type $parent The parent named role
     * @param type $child The child named role
     * @param type $alt Set to TRUE to remove from a parent permission, else from a parent role (default FALSE)
     * @return type
     */
    public function actionRemoveChildPermission($parent, $child, $alt = FALSE)
    {
        return $this->_fnChild(['proc' => 'removeChild', 'from' => $alt == FALSE ? 'role' : 'permisison', 'to' => 'permission', 'parent' => $parent, 'child' => $child]);
    }

    public function actionRemoveRoleChildren($name)
    {
        return $this->_fnItem(['type' => 'role', 'name' => $name, 'fn' => 'removeChildren']);
    }

    public function actionRemovePermissionChildren($name)
    {
        return $this->_fnItem(['type' => 'permission', 'name' => $name, 'fn' => 'removeChildren']);
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
     *                        Higher-Order Helpers
     * =========================================================================
     */

    /**
     * Private function used for assignment and revokement actions
     * 
     * @param type $email
     * @param type $link
     * @param type $sfn
     * @param type $lfn - The function used for linking
     * @return type
     */
    private function _fnUser($email, $fn, $role = NULL)
    {
        //Get User Model ID
        $userId = $this->_getUserId($email);
        //Exit when user not found
        if (!isset($userId)) {
            return $this->_exitCode;
        }
        //Get AuthItem (i.e. role or permission)
        if (isset($role)) {
            $item = $this->_authManager->getRole($role);
            //Exit when item not found
            if (!isset($item)) {
                $this->_exitCode = '300';
                $this->_msg = 'No such Role';
                return $this->_exitCode;
            }
        }
        //Try linking function
        try {
            if (isset($role)) {
                $this->_authManager->$fn($item, $userId);
            } else {
                $this->_authManager->$fn($userId);
            }
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }
        //Exit with try-catch results
        return $this->exitCode;
    }

    /**
     * 
     * @param type $config
     * @return int Exit code, 0 on success, else the error code is returned
     */
    private function _fnItem($config)
    {

        $isNew = isset($config['new']) && $config['new'] == TRUE;
        $item = $this->_getItem($config['type'], $config['item'], !$isNew);

        if ($this->_exitCode != 0) {
            return $this->_exitCode;
        }
        //Create new auth item if required 
        if ($isNew) {
            $createFn = 'create' . ucfirst($config['type']);
            $item = $this->_authManager->$createFn($config['item']);
        }

        try {
            $this->_authManager->$config['fn']($item);
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }


        return $this->_exitCode;
    }

    /**
     * 
     * @param type $config
     * @return type
     */
    private function _fnChild($config)
    {
        $parent = $this->_getItem($config['from'], $config['parent']);

        if ($this->_exitCode != 0) {
            return$this->_exitCode;
        }
        $child = $this->_getItem($config['from'], $config['parent']);
        if ($this->_exitCode != 0) {
            return$this->_exitCode;
        }
        //Try linking function
        try {
            $this->_authManager->$config['proc']($parent, $child);
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }
        //Exit with try-catch results
        return $this->exitCode;
    }

    /*
     * =========================================================================
     *                              Helpers
     * =========================================================================
     */

    /**
     * Finds user-id using account email address
     * 
     * @param type $email (Unique) email linked to user account.
     * @return int|NULL The user id if found, else NULL is returned
     */
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

    /**
     * 
     * @param type $type
     * @param type $name
     * @return type
     */
    private function _getItem($type, $name, $success = TRUE)
    {
        $fn = 'get' . ucfirst($type);
        try {
            $item = $this->_authManager->$fn($name);
            //Populate error message when success flag is met
            if (isset($item) != $success) {
                $this->_exitCode = '40' . $success == true ? '0' : '1';
                $this->_msg = ucfirst($type) . ' with name ' . $name . $success ? ' does not ' : ' already ' . 'exists';
            }
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }

        return $item;
    }

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
