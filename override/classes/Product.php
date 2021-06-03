<?php
class Product extends ProductCore {

    public $threelib;

    public function __construct(
        $id_product = null,
        $full = false,
        $id_lang = null,
        $id_shop = null,
        Context $context = null
    ) {
        $is_threelib_loggedIn = !empty(Configuration::get('THREELIB_TMP'));
        if ($is_threelib_loggedIn){
            self::$definition['fields']['threelib'] = array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'required' => false,
                'validate' => 'isCleanHtml'
            );
        }
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}