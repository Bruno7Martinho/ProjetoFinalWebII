<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registra um novo usuário no sistema
     */
    public function registrar($nome, $sexo, $fone, $email, $senha) {
        // Verifica se email já existe
        if ($this->emailExiste($email)) {
            throw new Exception("Este email já está cadastrado.");
        }

        $query = "INSERT INTO " . $this->table_name . " 
                 (nome, sexo, fone, email, senha, data_criacao) 
                 VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($senha, PASSWORD_BCRYPT);
        
        if ($stmt->execute([$nome, $sexo, $fone, $email, $hashed_password])) {
            return [
                'id' => $this->conn->lastInsertId(),
                'nome' => $nome,
                'email' => $email,
                'status' => 'success'
            ];
        }
        
        throw new Exception("Erro ao registrar usuário.");
    }

    /**
     * Realiza login do usuário
     */
    public function login($email, $senha) {
        $query = "SELECT id, nome, sexo, fone, email, senha, data_criacao 
                 FROM " . $this->table_name . " 
                 WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Remove a senha do array antes de retornar
            unset($usuario['senha']);
            return $usuario;
        }
        
        return false;
    }

    /**
     * Verifica se email já existe
     */
    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Cria usuário (alias para registrar)
     */
    public function criar($nome, $sexo, $fone, $email, $senha) {
        return $this->registrar($nome, $sexo, $fone, $email, $senha);
    }

    /**
     * Lista todos os usuários
     */
    public function ler($limit = null, $offset = 0) {
        $query = "SELECT id, nome, sexo, fone, email, data_criacao 
                 FROM " . $this->table_name;
        
        if ($limit !== null) {
            $query .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca usuário por ID
     */
    public function lerPorId($id) {
        $query = "SELECT id, nome, sexo, fone, email, data_criacao 
                 FROM " . $this->table_name . " 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca usuário por email
     */
    public function lerPorEmail($email) {
        $query = "SELECT id, nome, sexo, fone, email, data_criacao 
                 FROM " . $this->table_name . " 
                 WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza dados do usuário
     */
    public function atualizar($id, $nome, $sexo, $fone, $email, $senha = null) {
        // Verifica se o usuário existe
        $usuario_existente = $this->lerPorId($id);
        if (!$usuario_existente) {
            throw new Exception("Usuário não encontrado.");
        }

        // Verifica se email já está sendo usado por outro usuário
        if ($email !== $usuario_existente['email'] && $this->emailExiste($email)) {
            throw new Exception("Este email já está sendo usado por outro usuário.");
        }

        if ($senha) {
            $query = "UPDATE " . $this->table_name . " 
                     SET nome = ?, sexo = ?, fone = ?, email = ?, senha = ? 
                     WHERE id = ?";
            $hashed_password = password_hash($senha, PASSWORD_BCRYPT);
            $params = [$nome, $sexo, $fone, $email, $hashed_password, $id];
        } else {
            $query = "UPDATE " . $this->table_name . " 
                     SET nome = ?, sexo = ?, fone = ?, email = ? 
                     WHERE id = ?";
            $params = [$nome, $sexo, $fone, $email, $id];
        }

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Exclui usuário
     */
    public function deletar($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Altera senha do usuário
     */
    public function alterarSenha($id, $nova_senha) {
        $hashed_password = password_hash($nova_senha, PASSWORD_BCRYPT);
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hashed_password, $id]);
    }

    /**
     * Busca usuários por nome (search)
     */
    public function buscarPorNome($nome) {
        $query = "SELECT id, nome, sexo, fone, email, data_criacao 
                 FROM " . $this->table_name . " 
                 WHERE nome LIKE ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['%' . $nome . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Conta total de usuários
     */
    public function contarTotal() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Verifica credenciais sem fazer login
     */
    public function verificarCredenciais($email, $senha) {
        $query = "SELECT senha FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario && password_verify($senha, $usuario['senha']);
    }
}
?>