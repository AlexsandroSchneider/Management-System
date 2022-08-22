<?php 
$hash = password_hash('admin', PASSWORD_DEFAULT);
echo 'Hash gerado: ' . $hash . '<br><br>';
//echo 'Informações sobre o Hash gerado: <br>';
//var_dump(password_get_info($hash));
//$options = array('cost' => 11); // aqui poderia ser informado o salt: 'salt' =>
//// 'u85YimNH9IbppexoPkz155'
//echo '<br><br>Verifica se o Hash não foi gerado com as opções informadas: ' .
//password_needs_rehash($hash, PASSWORD_DEFAULT, $options) . '<br><br>';
//echo 'Resultado da comparação: ' . password_verify('marina', $hash);
$pw = '$2y$10$wTwgw6k6kypywcEsaEbk2OR6YJ0xv8dUuyks3pTAa5RiIF4uudgFm';
echo 'Resultado da comparação: ' . password_verify('admin', $pw);
?>