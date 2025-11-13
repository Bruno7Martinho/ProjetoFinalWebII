<?php
class Noticia {
    private $conn;
    private $table_name = "noticias";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Cria uma nova notícia
     */
    public function criar($titulo, $noticia, $autor, $imagem = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (titulo, noticia, autor, imagem, data) 
                 VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$titulo, $noticia, $autor, $imagem])) {
            return [
                'id' => $this->conn->lastInsertId(),
                'titulo' => $titulo,
                'status' => 'success'
            ];
        }
        
        throw new Exception("Erro ao criar notícia.");
    }

    /**
     * Lista todas as notícias
     */
    public function ler($limit = null, $offset = 0) {
        $query = "SELECT n.*, u.nome as autor_nome 
                 FROM " . $this->table_name . " n 
                 JOIN usuarios u ON n.autor = u.id 
                 ORDER BY n.data DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca notícia por ID
     */
    public function lerPorId($id) {
        $query = "SELECT n.*, u.nome as autor_nome 
                 FROM " . $this->table_name . " n 
                 JOIN usuarios u ON n.autor = u.id 
                 WHERE n.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca notícias por autor
     */
    public function lerPorAutor($autor_id) {
        $query = "SELECT n.*, u.nome as autor_nome 
                 FROM " . $this->table_name . " n 
                 JOIN usuarios u ON n.autor = u.id 
                 WHERE n.autor = ? 
                 ORDER BY n.data DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$autor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca notícias por título (search)
     */
    public function buscarPorTitulo($titulo) {
        $query = "SELECT n.*, u.nome as autor_nome 
                 FROM " . $this->table_name . " n 
                 JOIN usuarios u ON n.autor = u.id 
                 WHERE n.titulo LIKE ? 
                 ORDER BY n.data DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['%' . $titulo . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza notícia
     */
    public function atualizar($id, $titulo, $noticia, $imagem = null) {
        $noticia_existente = $this->lerPorId($id);
        if (!$noticia_existente) {
            throw new Exception("Notícia não encontrada.");
        }

        // Se não foi enviada nova imagem, mantém a atual
        if ($imagem === null) {
            $imagem = $noticia_existente['imagem'];
        }

        $query = "UPDATE " . $this->table_name . " 
                 SET titulo = ?, noticia = ?, imagem = ? 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$titulo, $noticia, $imagem, $id]);
    }

    /**
     * Atualiza apenas a imagem da notícia
     */
    public function atualizarImagem($id, $imagem) {
        $query = "UPDATE " . $this->table_name . " SET imagem = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$imagem, $id]);
    }

    /**
     * Remove imagem da notícia
     */
    public function removerImagem($id) {
        $noticia = $this->lerPorId($id);
        if ($noticia && $noticia['imagem']) {
            // Remove o arquivo físico se existir
            if (file_exists($noticia['imagem'])) {
                unlink($noticia['imagem']);
            }
        }
        
        return $this->atualizarImagem($id, null);
    }

    /**
     * Exclui notícia
     */
    public function deletar($id) {
        $noticia = $this->lerPorId($id);
        if ($noticia && $noticia['imagem'] && file_exists($noticia['imagem'])) {
            unlink($noticia['imagem']);
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Conta total de notícias
     */
    public function contarTotal($autor_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        if ($autor_id !== null) {
            $query .= " WHERE autor = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$autor_id]);
        } else {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Verifica se o usuário é o autor da notícia
     */
    public function isAutor($noticia_id, $usuario_id) {
        $noticia = $this->lerPorId($noticia_id);
        return $noticia && $noticia['autor'] == $usuario_id;
    }
}
?>