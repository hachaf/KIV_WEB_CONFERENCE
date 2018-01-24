<?php

require_once('dbBase.php');
include_once('./model/post.php');

/**
 * Class dbAssignment
 */
class dbAssignment extends dbBase {

    public $connection; 	// tam si ulozim aktualni spojeni
    private $connection_type = 0;

    function dbConUser() {
        $this->connection_type = DB_CONNECTION_USE_PDO_MYSQL;
    }

    function assignPost($postId, $reviewerId) {
        $query = "INSERT INTO ASSIGNMENT (POST_ID, REVIEWER_ID) VALUES (:postId, :reviewerId);";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':reviewerId', $reviewerId);
        $stmt->execute();
    }

    function unassignPost($postId, $reviewerId) {
        $query = "DELETE FROM ASSIGNMENT WHERE POST_ID = :postId AND REVIEWER_ID = :reviewerId;";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':reviewerId', $reviewerId);
        $stmt->execute();
    }

    function getAssignedPosts($reviewerId) {
        $query = "SELECT * FROM POST WHERE ID IN (SELECT POST_ID FROM ASSIGNMENT WHERE REVIEWER_ID = :reviewerId);";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':reviewerId', $reviewerId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $posts = array();
        foreach ($rows as $row) {
            array_push($posts, new post($row));
        }
        return $posts;
    }

    function getReviewers($postId) {
        $query = "SELECT * FROM CONUSER WHERE ID IN (SELECT REVIEWER_ID FROM ASSIGNMENT WHERE POST_ID = :postId);";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reviewers = array();
        foreach ($rows as $row) {
            array_push($reviewers, new post($row));
        }
        return $reviewers;
    }

    function hasAssigned($postId, $reviewerId) {
        $query = "SELECT COUNT(1) FROM ASSIGNMENT WHERE POST_ID = :postId AND REVIEWER_ID = :reviewerId;";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':reviewerId', $reviewerId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = array_pop($rows);
        $count = array_pop($row);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

}
