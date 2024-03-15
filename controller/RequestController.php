<?php

require '../lib/Produto.php'; //Precisei dar o required de qualquer jeito para acessar, e ainda tive que voltar pq o lib está do diretório de cima

use Lib\ProdutoManager;

$product_manager = new ProdutoManager();

$request = $_POST["request"]; //Me diz o que o cara quer

switch ($request) {

    case "cadastro":

        echo json_encode($product_manager->cadastro($_POST["data"]));
        break;

    case "update":
        
        echo json_encode($product_manager->update($_POST["data"]));
        break;

    case "delete":

        echo json_encode($product_manager->delete($_POST["data"]));
        break;

    case "requirement":

        echo json_encode($product_manager->retrieve($_POST["data"]));
        break;
    
    case "listar":

        echo json_encode($product_manager->listar($_POST["data"]));
        break;

    default: 
        
        echo "Erro"; 
        break;
}
