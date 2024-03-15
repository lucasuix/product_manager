<?php

namespace Lib;

require 'Conn.php';

//TENHO QUE FAZER OS RETORNOS, PARA SABER SE DEU BOM OU NÃO

class ProdutoManager {

    //Chama a conexão do banco de dados
    public function __construct() {

        session_start();
        $this->conn = new Conn();
        $this->conn->start();

    }

    public function cadastro(array $data) {

        //Verificação dos dados, se são válidos

        //Tratamento das chaves de valor, para terem o : na frente
        $treated_data = $this->prepareData($data);
        $result = array("cadastro" => true);

        //Cadastro, aqui tem que ter um try, pois pode dar um erro, aí o cadastro seria false
        try {
            $this->conn->cadastro($data);
        }
        catch (Exception $e) {
            $result["cadastro"] = false;
        }
        return $result; //Retorna um object falando se o cadastro foi bem sucedido ou não
        

    }

    public function update(array $data) {

        //Verificação dos dados e chaves

        //Tratamento das chaves de valor, data tem que ser enviado com as chaves :assim
        //$treated_data = $this->prepareData($data);
        
        $result = array("update" => true);
        
        //Atualização
        try {
            $this->conn->update($data);
        }
        catch (Exception $e) {
            $result["update"] = false;
        }
        return $result; //Retorna um object falando se o update foi bem sucedido ou não

    }

    public function delete(array $data) {

        //Tratamento da chave de valor
        $id = $data["id"];
        $id = intval($id);

        //Exclusão
        //Aqui tem que ter um try pq pode ser que não tenha esse cara no banco de dados
        $result = array("delete" => true);
        
        try {
            $this->conn->delete($id);
        }
        catch (Exception $e) {
            $result["delete"] = false;
        }
        return $result; //Retorna um object falando se o delete foi bem sucedido ou não

    }

    public function retrieve(array $data) {

        $part_sql = $this->makeSQL($data);
        $limit_offset = $this->conn->countRows($part_sql, $data);
        $remainder = $limit_offset % 12;
        $limit_offset = intval($limit_offset/12);
        
        if ($remainder > 0) $limit_offset = $limit_offset + 1;
        
        $_SESSION["query"] = $part_sql;
        $_SESSION["limit_offset"] = $limit_offset;
        
        $sql = "SELECT * FROM produtos " . $part_sql;
        
        return array(
            "resultados" => $this->conn->retrieve($sql, $data), 
            "offset" => $limit_offset
        );
    }

    //Retorna uma quantidade específica de produtos
    public function listar($data) {
        
        //Aqui eu pego o sql da sessão e adiciono o OFFSET, apena
        
        $sql = "SELECT * FROM produtos " . $_SESSION["query"] . " OFFSET :offset";
        
        return array(
            "resultados" => $this->conn->listar($sql, $data),
            "offset" => $_SESSION["limit_offset"]
        );

    }

    public function prepareData(array $data) {
        
        $treated_data;

        //Retorna um novo array com as chaves :
        foreach($data as $key => $value) {
            
            $treated_data[":$key"] = $value;
        }
        
        return $treated_data;

    }
    
    public function makeSQL(array $data) {
        
        // FAZ O SQL COMEÇANDO SEM O SELECT FROM
        $sql = "";
        
        
        $first = true;
        
        if ($data["buscar"]) {
            
            $sql .= "WHERE nome LIKE :nome ";
            
        }
        
        if ($data["quantidade"]["value"] == "true" ||
            $data["preco"]["value"] == "true") {
        
            $sql .= "ORDER BY ";
        
        }   
        
        if ($data["quantidade"]["value"] == "true") {
        
            $sql .= "quantidade ";
            
            switch($data["quantidade"]["order"]) {
                
                case "ASC":
                    
                    $sql .= "ASC";
                    break;
                
                case "DESC":
                
                    $sql .= "DESC";
                    break;
                    
                default:
                
                    return;
                    break;
            
            }
            
            $first = false;
        }
        
        
        if ($data["preco"]["value"] == "true") {
            
            if (!$first) $sql .= ", ";
            
            $sql .= " preco ";
            
            switch($data["preco"]["order"]) {
                
                case "ASC":
                    
                    $sql .= "ASC";
                    break;
                
                case "DESC":
                
                    $sql .= "DESC";
                    break;
                    
                default:
                
                    return;
                    break;
            
            }
            
        }
        
        $sql .= " LIMIT 12";
        
        return $sql;
        
    }

}
