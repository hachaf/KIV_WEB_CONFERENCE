<?php

require_once('dbBase.php');
include_once('./model/post.php');

/**
 * Class dbConUser
 * Napojeni modelu ConUser na databazi
 */
class dbPost extends dbBase {

    public $connection;
    private $connection_type = 0;

    function dbConUser() {
        $this->connection_type = DB_CONNECTION_USE_PDO_MYSQL;
    }

    function getAll() {
        $query = "SELECT * FROM POST;";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $posts = array();
        foreach ($rows as $row) {
            array_push($posts, new post($row));
        }
        return $posts;
    }

    function getById($id) {
        $query = "SELECT * FROM POST WHERE ID = :id;";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(":id", $id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($rows) == 0) {
            return null;
        } else {
            $model = new post($rows[0]);
            return $model;
        }
    }

    function create($post) {
        if ($post == null) return null;
        $authorId = $post->getAuthorID();
        $abstract = $post->getAbstract();
        $filename = $post->getFilename() != null ? $post->getAuthorID() : 'NULL';
        $publicated = (new \DateTime())->format('Y-m-d H:i:s');
        $state = $post->getState();
        $title = $post->getTitle();

        $query = "INSERT INTO POST (AUTHOR_ID, ABSTRACT, FILENAME, PUBLICATED, STATE, TITLE) 
                  VALUES (:authorId, :abstract, :filename, :publicated, :state, :title);";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(":authorId", $authorId);
        $statement->bindParam(":abstract", $abstract);
        $statement->bindParam(":publicated", $publicated);
        $statement->bindParam(":filename", $filename);
        $statement->bindParam(":state", $state);
        $statement->bindParam(":title", $title);

        $statement->execute();
        $item_id = $this->connection->lastInsertId();
        $post->setID($item_id);
        return $post;
    }

    function update($post) {
        if ($post == null) return null;
        $authorId = $post->getAuthorID();
        $abstract = $post->getAbstract();
        $filename = $post->getFilename();
        $publicated = "STR_TO_DATE('" . $post->getPublicated() . "', '%Y-%m-%d')";
        $title = $post->getTitle();
        $state = $post->getState() ? 1 : 0;
        $id = $post->getID();

        $stmt_text = "UPDATE POST SET AUTHOR_ID = :authorId, ABSTRACT = :abstract, 
                      FILENAME = :filename, PUBLICATED = :publicated, TITLE = :title, STATE = :state
                      WHERE ID = :id;";
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(":authorId", $authorId);
        $stmt->bindParam(":abstract", $abstract);
        $stmt->bindParam(":publicated", $publicated);
        $stmt->bindParam(":filename", $filename);
        $stmt->bindParam(":state", $state);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $post;
    }

    function remove($id) {
        if (!is_int($id)) return null;
        $stmt_text = 'DELETE FROM POST WHERE ID = ' . $id . ';';
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    function getByAuthor($authorId) {
        $query = "SELECT * FROM POST WHERE AUTHOR_ID = :authorId;";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(":authorId", $authorId);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $posts = array();
        foreach ($rows as $row) {
            array_push($posts, new post($row));
        }
        return $posts;
    }

    function getPublished() {
        $query = "SELECT * FROM POST WHERE STATE = 1;";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $posts = array();
        foreach ($rows as $row) {
            array_push($posts, new post($row));
        }
        return $posts;
    }

    function publishPost($postId) {
        $query = "UPDATE POST SET STATE = 1 WHERE ID = :postId;";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(":postId", $postId);
        $statement->execute();
    }

}

?>