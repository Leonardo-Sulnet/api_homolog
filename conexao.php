<?php
//Acessso ao servidor de replicação do MK
$servidor = "187.109.17.5";
$usuario = "cliente_r";
$senha ="Cl13nt_R";

try {

$conn = new PDO("pgsql:host=187.109.17.5 dbname=mkData3.0 user=cliente_r password=Cl13nt_R");
$conn->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die($e->getMessage());
}
   //echo "conexão BD ok";


//Acessso ao servidor de API
try {
$conn_api = new PDO("pgsql:host=192.168.167.38 dbname=bd_api user=user_api password=HGDYA231gf");
$conn_api->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die($e->getMessage());
}

?>