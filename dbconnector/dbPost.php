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

    function getAll()
    {
        $query = "select * from post;";
        $statement = $this->connection->prepare($query);
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    function getById($id) {
        $query = "select * from post where id = $id;";
        $statement = $this->connection->prepare($query);
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

        $pars = array();
        $pars["AUTHOR_ID"] = $post->getAuthorID();
        $pars["ABSTRACT"] = $post->getAbstract();
        $pars["FILENAME"] = $post->getFilename();
        $pars["PUBLICATED"] = $post->getPublicated();
        $pars["STATE"] = $post->getState();
        $pars["TITLE"] = $post->getTitle();

        $insert_columns = "";
        $insert_values  = "";

        if ($pars != null)
            foreach ($pars as $column => $value)
            {
                // pridat carky
                if ($insert_columns != "") $insert_columns .= ", ";
                if ($insert_columns != "") $insert_values .= ", ";

                $insert_columns .= "`$column`";
                $insert_values .= "?";
            }

        $stmt_text = "insert into `post` ($insert_columns) values ($insert_values);";
        $stmt = $this->connection->prepare($stmt_text);
        $bind_param_number = 1;

        foreach ($pars as $column => $value) {
            $stmt->bindValue($bind_param_number, $value);  // vzdy musim dat value, abych si nesparoval promennou (to nechci)
            $bind_param_number ++;
        }

        $stmt->execute();

        $item_id = $this->connection->lastInsertId();
        $post->setID($item_id);
        return $post;
    }

    function update($post) {
        if ($post == null) return null;

        $pars = array();
        $pars["AUTHOR_ID"] = $post->getAuthorID();
        $pars["ABSTRACT"] = "'" . $post->getAbstract() . "'";
        $pars["FILENAME"] = "'" . $post->getFilename() . "'";
        $pars["PUBLICATED"] = "STR_TO_DATE('" . $post->getPublicated() . "', '%Y-%m-%d')";
        $pars["TITLE"] = "'" . $post->getTitle() . "'";
        if ($post->getState()) {
            $pars["STATE"] = "1";
        } else {
            $pars["STATE"] = "0";
        }
        $pars["ID"] = $post->getID();

        $insert_columns = "";
        $insert_values  = "";

        foreach ($pars as $column => $value) {
            if ($insert_columns != "") $insert_columns .= ", ";
            if ($insert_columns != "") $insert_values .= ", ";

            $insert_columns .= "`$column`";
            $insert_values .= "?";
        }

        $stmt_text = 'UPDATE POST SET AUTHOR_ID = '
            . $pars["AUTHOR_ID"]
            . ', ABSTRACT = ' . $pars["ABSTRACT"]
            . ', FILENAME = ' . $pars["FILENAME"]
            . ', PUBLICATED = ' . $pars["PUBLICATED"]
            . ', TITLE = ' . $pars["TITLE"]
            . ', STATE = ' . $pars["STATE"]
            .' WHERE ID = ' .  $pars["ID"] . ';';

        $stmt = $this->connection->prepare($stmt_text);
        $bind_param_number = 1;

        foreach ($pars as $column => $value) {
            $stmt->bindValue($bind_param_number, $value);
            $bind_param_number ++;
        }

        $stmt->execute();
        return $post;
    }

    function remove($id) {
        if (!is_int($id)) return null;
        $stmt_text = 'DELETE FROM POST WHERE ID = ' . $id . ';';
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindValue(1, $id);
        echo $stmt_text;
        $stmt->execute();
    }

    function getByAuthor($authorId) {
        $query = "select * from post where author_id = :authorId;";
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

}

?>