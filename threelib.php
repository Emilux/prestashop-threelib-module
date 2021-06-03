<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_."threelib/classes/constantes.php";
require_once _PS_MODULE_DIR_."threelib/classes/ApiAccess.php";
require_once _PS_MODULE_DIR_."threelib/classes/CurlCommand.php";
require_once _PS_MODULE_DIR_."threelib/classes/SqlCall.php";
require_once _PS_MODULE_DIR_."threelib/classes/Authentication.php";


class Threelib extends Module
{

    private $is_threelib_loggedIn;

    private $SqlCall;

    private $ApiAccess;

    private $Authentication;

    public function __construct()
    {
        $this->name = 'threelib';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Emilien FUCHS';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ThreeLIB Module');
        $this->description = $this->l('Module de l\'api TheeLIB');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');

        $this->is_threelib_loggedIn = !empty(Configuration::get('THREELIB_TMP'));
        $this->SqlCall = new SqlCall();
        $this->Authentication = new Authentication();
        $this->ApiAccess = new ApiAccess(API_URL_SITE, new CurlCommand(), $this->Authentication);

    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') ||
            ! $this->registerHook('displayHeader') ||
            !$this->registerHook('displayAdminProductsMainStepLeftColumnMiddle') ||
            !$this->registerHook('actionProductUpdate') ||
        !Configuration::updateValue('THREELIB_USER', '') ||
            !Configuration::updateValue('THREELIB_TMP', '')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            ! $this->SqlCall->_unInstallSql() ||
            !Configuration::deleteByName('THREELIB_USER') ||
            !Configuration::deleteByName('THREELIB_TMP')
        ) {
            return false;
        }

        return true;
    }

    public function getContent(){

        //Récuperer le lien du module
        $link = new Link();
        $module_link = $link->getAdminLink('AdminModules', false, [], ['configure' => 'threelib', 'token' => Tools::getAdminTokenLite('AdminModules')]);

        //Vérifie si le formulaire de config est envoyé et que l'utilisateur n'est pas connecté
        if (Tools::isSubmit('btnSubmit') && !$this->is_threelib_loggedIn){

            $response = $this->ApiAccess->login(API_PATH.LOGIN_PATH,array("username"=>Tools::getValue('username'), "password"=>Tools::getValue('password')));
            if (!isset($response['error'])){
               if ($response['http_code'] === 200 || $response['http_code'] === 204){
                   Configuration::updateValue("THREELIB_TMP", md5(Tools::getValue('username')));
                   if (isset($response['user_path'])){
                       Configuration::updateValue("THREELIB_USER", $response['user_path']);
                       $this->SqlCall->_installSql();
                   }
               } else {
                   unlink(TMP_PATH.DIRECTORY_SEPARATOR.md5(Tools::getValue('username')));
               }
            }
            Tools::redirect($module_link);
        }

        if (Tools::getValue('action') === 'logout' && $this->is_threelib_loggedIn){
            $this->Authentication->logout();
        }

        if ( $this->is_threelib_loggedIn){
            $this->context->smarty->assign('module_link', $module_link);
            return $this->display(__FILE__, "views/templates/admin/admin.tpl");
        } else {
            return $this->displayForm();
        }




    }

    public function displayForm()
    {
        // Récupère la langue par défaut
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Initialise les champs du formulaire dans un tableau
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Connexion à threelib'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Adresse email utilisée sur threelib : '),
                        'name' => 'username',
                        'required' => true
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->l('Mot de passe utilisé sur threelib :'),
                        'name' => 'password',
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Login'),
                    'name'  => 'btnSubmit'
                )
            ),
        );

        $helper = new HelperForm();

        // Module, token et currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Langue
        $helper->default_form_language = $defaultLang;

        $helper->fields_value['username'] = '';

        return $helper->generateForm(array($form));
    }

    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) {

        if ($this->is_threelib_loggedIn) {
            $product = new Product($params['id_product']);
            $elements = $this->ApiAccess->getElement(Configuration::get('THREELIB_USER'),Configuration::get('THREELIB_TMP'));

            $models = $elements['models'];

            $this->context->smarty->assign(array(
                'threelib' => $product->threelib,
                'threelib_elements' => $models,
            ));
            return $this->display(__FILE__, 'views/templates/hook/extrafields.tpl');
        }

        return false;
    }


    public function hookDisplayHeader($params)
    {

        return $this->display(__FILE__, 'modelviewer.tpl');
    }


}