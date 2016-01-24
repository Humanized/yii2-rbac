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

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        $this->_userClass = $module->params['identityClass'];
        $this->_findUser = $module->params['fnUser'];
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

}
