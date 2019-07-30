<?php

class AuthController extends AuthControllerCore
{
    protected function processSubmitAccount(){
        if ( Tools::getValue('doc_type') == 1 ){
            $postDoc = Tools::getValue('cnpj');
            $rg_id = Tools::getValue('nie');
        }else{
            $postDoc = Tools::getValue('cpf');
            $rg_id = Tools::getValue('rg');
        }
        
        $docNumber = preg_replace("/[^0-9]/", "", $postDoc);
        $_POST['document'] = $docNumber;
        
        $docRgIe = preg_replace("/[^0-9]/", "", $rg_id);
        $_POST['rg_ie'] = $docRgIe;
        
        $_POST['doc_type'] = Tools::getValue('doc_type');
        
        if( Tools::getValue('validDoc') == 'false' ){
            $this->errors[] = Tools::displayError('Número de documento inválido, por favor verifique.');
        }
        
        parent::processSubmitAccount();
    }

}

