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
 * @package yii2-rbac
 *
 */
class AdminController extends Controller {

    /**
     * Private pointer to Identity instance (setup through module configuration)
     * @var \yii\rbac\DbManager 
     */
    private $_userClass;

    /**
     * Private pointer to DbManager instance (setup on construction)
     * @var \yii\rbac\DbManager 
     */
    private $_auth;

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        $this->_userClass = $module->params['identityClass'];
        $this->_auth = \Yii::$app->authManager;
    }

    /*
     * =========================================================================
     *                Operations dealing with User-Role Linkage
     * =========================================================================
     */

    /**
     * Assigns a role to a user
     * 
     * @param string $role  Named role
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code
     */
    public function actionAssign($role, $email)
    {
        return $this->_fnUser(['email' => $email, 'fn' => 'assign', 'role' => $role]);
    }

    /**
     * Revokes a role from a user
     * 
     * @param string $role  Named role
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code is returned
     */
    public function actionRevoke($role, $email)
    {
        return $this->_fnUser(['email' => $email, 'fn' => 'revoke', 'role' => $role]);
    }

    /**
     * Revokes all roles from a user
     * 
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code is returned
     */
    public function actionRevokeAll($email)
    {
        return $this->_fnUser(['email' => $email, 'fn' => 'revokeAll']);
    }

    /**
     * Returns all role assignment information for the specified user.
     * 
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code is returned
     */
    public function actionGetAssignments($email)
    {
        return $this->_fnUser(['email' => $email, 'fn' => 'getAssignments', 'process' => TRUE]);
    }

    /**
     * Returns the roles that are assigned to the user via assign().
     * 
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code is returned
     */
    public function actionGetRolesByUser($email)
    {
        return $this->_fnUser(['email' => $email, 'fn' => 'getRolesByUser', 'process' => TRUE]);
    }

    /**
     * Returns all permissions that the user has
     * 
     * @param string $email (Unique) email linked to user account 
     * @return int exitCode - 0 default, else error code is returned 
     */
    public function actionPermissionByUser($email)
    {
        return $this->_fnUser(['email' => $email, 'fn' => 'getPermissionByUser', 'process' => TRUE]);
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

    /**
     * 
     * @param type $name
     * @return type
     */
    public function actionRemoveRoleChildren($name)
    {
        return $this->_fnItem(['type' => 'role', 'item' => $name, 'fn' => 'removeChildren']);
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    public function actionRemovePermissionChildren($name)
    {
        return $this->_fnItem(['type' => 'permission', 'item' => $name, 'fn' => 'removeChildren']);
    }

    /*
     * =========================================================================
     *                              Public Properties
     * =========================================================================
     */

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for success, else error code is returned
     */
    public function actionAssignmentTable()
    {
        return $this->_exitCode;
    }

    /**
     * Prints a list of named (default) roles to console output
     * @return int 0 for success, else error code is returned
     */
    public function actionDefaultRoles()
    {
        return $this->_exitCode;
    }

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for success, else error code is returned
     */
    public function actionItemChildTable()
    {
        return $this->_exitCode;
    }

    /**
     * Prints the name of the relevant RBAC database table name to console output 
     * @return int 0 for success, else error code is returned
     */
    public function actionItemTable()
    {
        return $this->_exitCode;
    }

    /**
     * Prints a list of named permissions to console output
     * @return int 0 for success, else error code is returned
     */
    public function actionPermissions()
    {
        return $this->_exitCode;
    }

    /**
     * Prints a list of named permissions to console output
     * @return int 0 for success, else error code is returned
     */
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
    private function _fnUser($config)
    {
        //Get User Model ID
        $userId = $this->_getUserId($config['email']);
        //Exit when user not found
        if (!isset($userId)) {
            return $this->_exitCode;
        }
        //Get auth item object
        $item = $this->_getItem('role', $config['role']);
        //Exit when item not found
        if (!isset($item)) {
            return $this->_exitCode;
        }

        $result = $this->_linkUser($userId, $item, $config);
        if (isset($config['process'])) {
            $this->_processResult($result);
        }
        //Try linking function
        //Exit with try-catch results
        return $this->_exitCode;
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
            $item = $this->_auth->$createFn($config['item']);
        }
        try {
            $result = $this->_auth->$config['fn']($item);
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }

        if (isset($config['process'])) {
            $this->_processResult($result);
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
        $child = $this->_getItem($config['to'], $config['child']);
        if ($this->_exitCode != 0) {
            return$this->_exitCode;
        }
        //Try linking function
        try {
            $this->_auth->$config['proc']($parent, $child);
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }
        //Exit with try-catch results
        return $this->_exitCode;
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
     * @param type $userId
     * @param type $item
     * @param type $config
     * @return type
     */
    private function _linkUser($userId, $item, $config)
    {
        try {
            $sugar = $this->_auth->$config['fn'];
            $result = (isset($config['role']) ? $sugar($item, $userId) : $sugar($userId));
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
            return NULL;
        }
        return $result;
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
            $item = $this->_auth->$fn($name);
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

    private function _processResult($result, $mapFn = NULL)
    {
        switch (gettype($result)) {
            case 'array': {
                    std_out('outputting ' . count($result) . ' values');
                    array_map($mapFn, $result);
                    break;
                }
            default: {
                    std_out('output:' . $result);
                    break;
                }
        }
    }

}
