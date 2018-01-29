<?php

require_once('dbBase.php');
include_once('./model/review.php');

class dbReview extends dbBase {

    public $connection;
    private $connection_type = 0;

    /**
     * Konstruktor
     */
    function dbConUser() {
        $this->connection_type = DB_CONNECTION_USE_PDO_MYSQL;
    }

    /**
     *  Načte všechny recenze v databázi a vrátí je v poli
     */
    function getAll() {
        $query = "SELECT * FROM REVIEW;";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = array();
        foreach ($rows as $row) {
            array_push($result, new review($row));
        }
        return $result;
    }

    /**
     * Najde v databázi recenzi podle id
     * @param $id id recenze
     * @return null|review
     */
    function getById($id) {
        if (!is_int($id)) return null;
        $query = "SELECT * FROM REVIEW WHERE ID = :id;";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($rows) == 0) {
            return null;
        } else {
            $model = new review($rows[0]);
            return $model;
        }
    }

    /**
     * Vytvoří v databázi novou řádku pro objekt v parametru
     * @param $review objekt recenze
     * @return null
     */
    function create($review) {
        if ($review == null) return null;
        $authorId = $review->getAuthorID();
        $postId = $review->getPostID();
        $text = $review->getText();
        $verdict = $review->getVerdict();
        $locked = $review->getLocked();
        $publicated = "STR_TO_DATE('" . $review->getPublicated() . "', '%Y-%m-%d')";

        $stmt_text = "INSERT INTO REVIEW (AUTHOR_ID, POST_ID, TEXT, VERDICT, LOCKED, PUBLICATED) VALUES
                      (:authorId, :postId, :text, :verdict, :locked, :publicated);";
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(':authorId', $authorId);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':verdict', $verdict);
        $stmt->bindParam(':locked', $locked);
        $stmt->bindParam(':publicated', $publicated);

        $stmt->execute();
        $item_id = $this->connection->lastInsertId();
        $review->setID($item_id);
        return $review;
    }

    /**
     * Updatuje recenzi v databázi
     * @param $review recenze
     * @return null
     */
    function update($review) {
        if ($review == null) return null;

        $postId = $review->getPostID();
        $text = $review->getText();
        $authorId = $review->getAuthorID();
        $publicated = "STR_TO_DATE('" . $review->getPublicated() . "', '%Y-%m-%d')";
        $verdict = $review->getVerdict() ? 1 : 0;
        $locked = $review->getLocked() ? 1 : 0;
        $id = $review->getID();

        $stmt_text = 'UPDATE REVIEW SET AUTHOR_ID = :authorId, POST_ID = :postId, TEXT = :text,
                      PUBLICATED = :publicated, LOCKED = :locked, VERDICT = :verdict 
                      WHERE ID = :id;';

        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(':authorId', $authorId);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':verdict', $verdict);
        $stmt->bindParam(':locked', $locked);
        $stmt->bindParam(':publicated', $publicated);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $review;
    }

    /**
     * Odstraní v datavázi recenzi se zadaným id
     * @param $id id recenze
     * @return null
     */
    function remove($id) {
        $stmt_text = 'DELETE FROM REVIEW WHERE ID = :id;';
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    function reviewsCount($postId) {
        $query = "SELECT COUNT(1) FROM REVIEW WHERE POST_ID = :postId;";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = array_pop($rows);
        $count = array_pop($row);
        return $count;
    }

    function hasReviewed($authorId, $postId) {
        $query = "SELECT COUNT(1) FROM REVIEW WHERE POST_ID = :postId AND AUTHOR_ID = :authorId;";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':authorId', $authorId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = array_pop($rows);
        $count = array_pop($row);
        return ($count > 0);
    }

}