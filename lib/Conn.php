<?php

namespace Lib;

use \PDO; //Tenho que chamar pq do contrário parece que o php acha que ele está no Lib também

class Conn {

    protected $pdo;
    
    protected $db_name = "estoque";
    
    //Credenciais do PC DO SENAI
    //protected $user = "root";
    //protected $password = "usbw";
    
    //Outras credenciais
    protected $user = "galartico";
    protected $password = "04x?62vFk53";

    //Conexão com o banco de dados
    public function __construct() {

        $this->start();

    }

    public function start() {
        //Descomente essa linha para funcionar no PC do SENAI
        //$this->pdo = new PDO("mysql:host=localhost;port=3307;dbname=". $this->db_name, $this->user, $this->password);
        
        //Comente essa linha para funcionar no PC do SENAI
        $this->pdo = new PDO("mysql:host=localhost;dbname=". $this->db_name, $this->user, $this->password);
        $this->pdo->exec("set names utf8");

    }

    public function cadastro(array $data) {

        //array => :nome, :descricao, :quantidade, :preco
        $stmt = $this->pdo->prepare("INSERT INTO produtos (nome, descricao, quantidade, preco) VALUES ( :nome, :descricao, :quantidade, :preco)");
        $stmt->execute($data);

    }

    public function update(array $data) {
        
        $id = $data["id"];
        unset($data["id"]);

        //Preparando o SQL que será utilizado
        $sql = "UPDATE produtos SET ";
        $first = true; // Variável para controlar se é a primeira chave
        
        
        foreach($data as $key => $value) {

            // Adiciona uma vírgula se não for a primeira chave
            if (!$first) $sql .= ", ";
            else $first = false;
            
            $sql .= "$key = :$key"; // Adiciona a chave ao SQL
        }
        
        $sql .= " WHERE id = :id"; //Termina gerando o SQL que a gente precisa


        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT); //Vincula o id, pois é o único que não tem na data
        
        if (array_key_exists("nome", $data))             $stmt->bindValue(':nome',            $data["nome"],        PDO::PARAM_STR);
        if (array_key_exists("descricao", $data))        $stmt->bindValue(':descricao',       $data["descricao"],   PDO::PARAM_STR);
        if (array_key_exists("quantidade", $data))       $stmt->bindValue(':quantidade',      $data["quantidade"],  PDO::PARAM_INT);
        if (array_key_exists("preco", $data))            $stmt->bindValue(':preco',           $data["preco"],       PDO::PARAM_STR);
        
        $stmt->execute();

    }

    public function delete($id) {

        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
        $stmt->execute(array(":id" => $id));

    }

    public function retrieve($sql, $data) {

        $stmt = $this->pdo->prepare($sql);
        
        if ($data['buscar']) {
            $nome = $data['buscar'];
            $stmt->bindValue(':nome', "%$nome%", PDO::PARAM_STR);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    
    //Só retorna o que já tem o offset
    public function listar($sql, $data) {

        $stmt = $this->pdo->prepare($sql);
        $offset = $data['offset']*12;
    
        
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        if ($data['buscar']) {
            $nome = $data['buscar'];
            $stmt->bindValue(':nome', "%$nome%", PDO::PARAM_STR);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    
    
    public function countRows($part_sql, $data) {
        
        $sql = "SELECT COUNT(*) AS total_linhas FROM produtos " . $part_sql;
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($data['buscar']) {
            $nome = $data['buscar'];
            $stmt->bindValue(':nome', "%$nome%", PDO::PARAM_STR);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_linhas'];
    }

}

/*
 * 
 *  ob_start();
    var_dump($sql);
    $output = ob_get_clean();
    error_log($output);
    die;

*/
