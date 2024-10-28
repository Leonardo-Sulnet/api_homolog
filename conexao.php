<?php

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
?>