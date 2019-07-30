<?php

class Customer extends CustomerCore
{
    public $document;
    public $rg_ie;
    public $doc_type;
    
    public function __construct($id = null, $id_lang = null, $id_shop = null) {
        
        $definition = self::$definition;
        $definition['fields']['document']   = array('type' => ObjectModelCore::TYPE_STRING, 'required' => true);
        $definition['fields']['rg_ie']      = array('type' => ObjectModelCore::TYPE_STRING);
        $definition['fields']['doc_type']   = array('type' => ObjectModelCore::TYPE_INT, 'required' => true);

        self::$definition = $definition;
        
        parent::__construct($id, $id_lang, $id_shop);
    }
    
}
