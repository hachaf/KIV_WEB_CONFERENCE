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
        $query = "select * from review;";
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
        $query = "select * from review where id = $id;";
        $statement = $this->connection->prepare($query);
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

        $pars = array();
        $pars["AUTHOR_ID"] = $review->getAuthorID();
        $pars["POST_ID"] = $review->getPostID();
        $pars["TEXT"] = $review->getText();
        $pars["VERDICT"] = $review->getVerdict();
        $pars["LOCKED"] = $review->getLocked();
        $pars["PUBLICATED"] = $review->getPublicated();

        $insert_columns = "";
        $insert_values  = "";

        if ($pars != null)
            foreach ($pars as $column => $value) {
                if ($insert_columns != "") $insert_columns .= ", ";
                if ($insert_columns != "") $insert_values .= ", ";
                $insert_columns .= "`$column`";
                $insert_values .= "?";
            }

        $stmt_text = "insert into `review` ($insert_columns) values ($insert_values);";
        $stmt = $this->connection->prepare($stmt_text);
        $bind_param_number = 1;

        foreach ($pars as $column => $value) {
            $stmt->bindValue($bind_param_number, $value);
            $bind_param_number ++;
        }

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

        $pars = array();

        $pars["POST_ID"] = $review->getPostID();
        $pars["TEXT"] = "'" . $review->getText() . "'";
        $pars["AUTHOR_ID"] = $review->getAuthorID();
        $pars["PUBLICATED"] = "STR_TO_DATE('" . $review->getPublicated() . "', '%Y-%m-%d')";
        if ($review->getVerdict()) {
            $pars["VERDICT"] = "1";
        } else {
            $pars["VERDICT"] = "0";
        }
        if ($review->getLocked()) {
            $pars["LOCKED"] = "1";
        } else {
            $pars["LOCKED"] = "0";
        }
        $pars["ID"] = $review->getID();

        $insert_columns = "";
        $insert_values  = "";

        foreach ($pars as $column => $value) {
            if ($insert_columns != "") $insert_columns .= ", ";
            if ($insert_columns != "") $insert_values .= ", ";
            $insert_columns .= "`$column`";
            $insert_values .= "?";
        }

        $stmt_text = 'UPDATE REVIEW SET AUTHOR_ID = '
            . $pars["AUTHOR_ID"]
            . ', POST_ID = ' . $pars["POST_ID"]
            . ', TEXT = ' . $pars["TEXT"]
            . ', PUBLICATED = ' . $pars["PUBLICATED"]
            . ', LOCKED = ' . $pars["LOCKED"]
            . ', VERDICT = ' . $pars["VERDICT"]
            .' WHERE ID = ' .  $pars["ID"] . ';';

        $stmt = $this->connection->prepare($stmt_text);
        $bind_param_number = 1;

        foreach ($pars as $column => $value) {
            $stmt->bindValue($bind_param_number, $value);
            $bind_param_number ++;
        }

        $stmt->execute();
        return $review;
    }

    /**
     * Odstraní v datavázi recenzi se zadaným id
     * @param $id id recenze
     * @return null
     */
    function remove($id) {
        if (!is_int($id)) return null;
        $stmt_text = 'DELETE FROM REVIEW WHERE ID = ' . $id . ';';
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

}