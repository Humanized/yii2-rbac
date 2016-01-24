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
class ManagerController extends Controller {


    private $_model;
    private $_userClass;
    private $_findUser;

    /**
     *
     * @var \yii\rbac\DbManager 
     */
    private $_authManager;
    private $_propertyMap = [
        'string' => ['assignment-table', 'item-child-table', 'item-table', 'rule-table']
    ];

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        $this->_userClass = $module->params['identityClass'];
        $this->_findUser = $module->params['fnUser'];
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
        echo $this->preventDefault;
        $print = NULL;
        $map = NULL;
        $this->_setupFunctions($action,$print, $map);
        $store = parent::afterAction($action, $result);

        if ($store == 0) {
            $this->$print($action->id, $map);
        }
        return $store;
    }

    private function _setupFunctions($action,&$print, &$map)
    {
        $this->_setupValueFunctions($action,$print, $map);
    }

    private function _setupValueFunctions($action,&$print, &$map)
    {
        if (in_array($action->id, $this->_propertyMap['string'])) {
            $map = function($x) {
                $procName = lcfirst(\yii\helpers\Inflector::id2camel($x));
                return $this->_authManager->$procName;
            };
            $print = 'printValue';
            $this->_msg = 'Printing property string to output...';
        }
    }

    private function _setupValueCollectionFunctions(&$print, &$map)
    {
        
    }

}
