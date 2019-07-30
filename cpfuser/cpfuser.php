<?php

if (!defined('_PS_VERSION_'))
	exit;

class CpfUser extends Module{
    
    private $required;
    
    public function __construct() {
        $this->name = 'cpfuser';
        $this->tab = 'front_office_features';
        $this->version = '1.6';
        $this->author = 'Line Host - www.linehost.com.br';
        $this->need_instance = 0;
        
        parent::__construct();
        
        $this->displayName = 'CPF Usuário';
        $this->description = 'Adicionar o campo CPF / CNPJ no cadastro do cliente';
        
        // Guardar informação no banco
        $this->required = 1;
        
    }
    
    public function install(){
        if ( !parent::install() || 
                !$this->addColumsCustomer() || 
                !$this->registerHook('createAccountTop') ){
            return false;
        }
        
        return true;
    }
    
    public function uninstall() {
        if ( !parent::uninstall() ){
            return false;
        }
        return true;
    }
    
    private function addColumsCustomer(){
        $exists = false;
        $column = array("document", "rg_ie","doc_type");
        $columns = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("show columns from " . _DB_PREFIX_."customer");

        foreach($columns as $value){
            if (in_array($column, $value) ){
                $exists = true;
            }
        }
        
        if($exists === false){
            try {
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'customer` ADD `document` VARCHAR( 14 ) NULL;');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'customer` ADD `rg_ie` VARCHAR( 20 ) NULL;');
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'customer` ADD `doc_type` int(1) unsigned NULL;');
            } catch (Exception $exc) {
                return false;
            }
        }
        
        return true;
    }
    
    public function hookcreateAccountTop()
    {
        global $smarty;
        $urlValidateDoc = Tools::getShopDomain(true, true) . $this->_path . 'validate.php';
        
        $this->context->controller->addJS($this->_path . 'maskedinput.js');
        $smarty->assign('required', $this->required);
        $smarty->assign('urlValidateDoc', $urlValidateDoc);
        return $this->display(__FILE__, 'documents.tpl');
    }

    public function cpfValidation($item)
    {
        $nulos = array("12345678909","11111111111","22222222222","33333333333",
            "44444444444","55555555555","66666666666", "77777777777",
            "88888888888", "99999999999", "00000000000");
        
        /* Retira todos os caracteres que nao sejam 0-9 */
        $cpf = preg_replace("/[^0-9]/", "", $item);
        $err = '';

        if (strlen($cpf) <> 11)
        {
             $err =  $this->l('O CPF deve conter 11 dígitos!');
        }
        if (!is_numeric($cpf))
        {
            $err =  $this->l('Apenas números são aceitos!');
        }

        /* Retorna falso se o cpf for nulo*/
        if (in_array($cpf, $nulos))
        {
             $err =  $this->l('CPF inválido!');
        }

        if($this->checkDuplicate($cpf) == true)
        {
             $err =  $this->l('Este CPF já está cadastrado!');
        }

        /*Calcula o penúltimo dígito verificador*/
        $acum = 0;
        for ($i = 0; $i < 9; $i++)
        {
            $acum += $cpf[$i] * (10 - $i);
        }

        $x = $acum % 11;
        $acum = ($x > 1) ? (11 - $x) : 0;
        /* Retorna falso se o digito calculado eh diferente do passado na string */
        if ($acum != $cpf[9])
        {
             $err =  $this->l('CPF inválido. Verifique por favor!');
        }
        /*Calcula o último dígito verificador*/
        $acum = 0;
        for ($i = 0; $i < 10; $i++)
        {
            $acum += $cpf[$i] * (11 - $i);
        }

        $x = $acum % 11;
        $acum = ($x > 1) ? (11 - $x) : 0;
        /* Retorna falso se o digito calculado eh diferente do passado na string */
        if ($acum != $cpf[10])
        {
             $err =  $this->l('CPF inválido. Verifique por favor!');
        }

        if ( !empty($err) ){
            $result = array("status" => 0, "error" => $err);
        }else{
            $result = array("status" => 1, "msg" => "CPF Válido!");
        }

        return Tools::jsonEncode($result);
    }

    function cnpjValidate($str)
    {
        $nulos = array("12345678909123","111111111111111","22222222222222","333333333333333",
            "44444444444444","55555555555555", "666666666666666","77777777777777",
            "88888888888888", "99999999999999","00000000000000");
        
        /* Retira todos os caracteres que nao sejam 0-9 */
        $cnpj = preg_replace("/[^0-9]/", "", $str);
        $err = '';
        
        if (strlen($cnpj) <> 14)
        {
             $err =  $this->l('O CNPJ deve conter 14 dígitos!');
        }
        
        if (!is_numeric($cnpj))
        {
            $err =  $this->l('Apenas números são aceitos!');
        }
        
        if($this->checkDuplicate($cnpj) == true)
        {
             $err =  $this->l('Este CNPJ já está cadastrado!');
        }
        
        if (in_array($cnpj, $nulos))
        {
             $err =  $this->l('CNPJ nulo. Verifique por favor!');
        }

        if (strlen($cnpj) > 14)
            $cnpj = substr($cnpj, 1);

        $sum1 = 0;
        $sum2 = 0;
        $sum3 = 0;
        $calc1 = 5;
        $calc2 = 6;

        for ($i=0; $i <= 12; $i++)
        {
            $calc1 = $calc1 < 2 ? 9 : $calc1;
            $calc2 = $calc2 < 2 ? 9 : $calc2;

            if ($i <= 11)
            $sum1 += $cnpj[$i] * $calc1;

            $sum2 += $cnpj[$i] * $calc2;
            $sum3 += $cnpj[$i];
            $calc1--;
            $calc2--;
        }

        $sum1 %= 11;
        $sum2 %= 11;

        $result = ($sum3 && $cnpj[12] == ($sum1 < 2 ? 0 : 11 - $sum1) && $cnpj[13] == ($sum2 < 2 ? 0 : 11 - $sum2)) ? true : false;
        
        if(!$result)
        {
            $err =  $this->l('CNPJ inválido. Verifique por favor!');
        }

        if ( !empty($err) ){
            $result = array("status" => 0, "error" => $err);
        }else{
            $result = array("status" => 1, "msg" => "CNPJ Válido!");
        }
        
        return Tools::jsonEncode($result);

    }
    
    public function checkDuplicate($value)
    {
        $db = Db::getInstance();
        $result = $db->getRow('
        SELECT document FROM `'._DB_PREFIX_.'customer`
        WHERE `document` = "'.$value.'"');

        return intval($result['document']) != 0 ? true : false;
    }
    
}

?>
