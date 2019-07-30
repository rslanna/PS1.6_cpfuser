<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
$cpfuser = Module::getInstanceByName('cpfuser');

$result = (Tools::getIsset('cpf') == true ? $cpfuser->cpfValidation(Tools::getValue('cpf')) : $cpfuser->cnpjValidate(Tools::getValue('cnpj')));

echo $result;

?>
