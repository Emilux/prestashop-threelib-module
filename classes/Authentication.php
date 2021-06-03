<?php

require_once _PS_MODULE_DIR_."threelib/classes/SqlCall.php";

class Authentication
{
    private $SqlCall;
    public function __construct()
    {
        $this->SqlCall = new SqlCall();
    }

    public function logout(){
        //Récuperer le lien du module
        $link = new Link();
        $module_link = $link->getAdminLink('AdminModules', false, [], ['configure' => 'threelib', 'token' => Tools::getAdminTokenLite('AdminModules')]);
        unlink(TMP_PATH.DIRECTORY_SEPARATOR.Configuration::get('THREELIB_TMP'));
        Configuration::updateValue("THREELIB_TMP", '');
        Configuration::updateValue("THREELIB_USER", '');
        $this->SqlCall->_unInstallSql();
        Tools::redirect($module_link);
    }

}