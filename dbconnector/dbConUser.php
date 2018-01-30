<?php

require_once('dbBase.php');
include_once('./model/conUser.php');

/**
 * Class dbConUser
 * Napojeni modelu ConUser na databazi
 */
class dbConUser extends dbBase {

    public $connection; 	// tam si ulozim aktualni spojeni
    private $connection_type = 0;

    function dbConUser() {
        $this->connection_type = DB_CONNECTION_USE_PDO_MYSQL;
    }

    /**
     *  Načíst všechny předměty. Z důvodu srozumitelnosti kombinuji češtinu a angličtinu.
     */
    function getAll()  {
        $query = "SELECT * FROM CONUSER;";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $users = array();
        foreach ($rows as $row) {
            array_push($users, new conUser($row));
        }
        return $users;
    }

    function getReviewers()  {
        $query = "SELECT * FROM CONUSER WHERE TYPE = 'REV';";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $users = array();
        foreach ($rows as $row) {
            array_push($users, new conUser($row));
        }
        return $users;
    }

    function getById($id) {
        $query = "SELECT * FROM CONUSER WHERE ID = :id;";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(":id", $id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($rows) == 0) {
            return null;
        } else {
            $model = new conUser($rows[0]);
            return $model;
        }
    }

    function create($user) {
        if ($user == null) return null;
        $login = $user->getLogin();
        $password = $user->getPassword();
        $type = $user->getType();
        $blocked = $user->getBlocked() ? 1 : 0;

        $stmt_text = "INSERT INTO CONUSER (LOGIN, PASSORD, TYPE, BLOCKED) values (:login, :password, :type, :blocked);";
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":blocked", $blocked);
        $stmt->execute();

        $item_id = $this->connection->lastInsertId();
        $user->setID($item_id);
        return $user;
    }

    function update($user) {
        if ($user == null) return null;

        $login = $user->getLogin();
        $password = $user->getPassword();
        $type = $user->getType();
        $blocked = $user->getBlocked() ? 1 : 0;
        $id = $user->getID();

        $stmt_text = 'UPDATE CONUSER SET LOGIN = :login, PASSWORD = :password, TYPE = :type, 
                      BLOCKED = :blocked WHERE ID = :id;';

        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":blocked", $blocked);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $user;
    }

    function remove($id) {
        $stmt_text = 'DELETE FROM CONUSER WHERE ID = :id;';
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }

    function getByNameAndPwd($username, $pwd) {
        $stmt = $this->connection->prepare("SELECT * FROM CONUSER WHERE (LOGIN = :login AND PASSWORD = :password AND BLOCKED != 1)");
        $stmt->bindParam(':login', $username);
        $stmt->bindParam(':password', $pwd);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res == null) return null;
        return new conUser($res);
    }

    function userExist($username) {
        $stmt = $this->connection->prepare("SELECT COUNT(1) FROM CONUSER WHERE (LOGIN = :login)");
        $stmt->bindParam(':login', $username);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_COLUMN, 0);
        if ($res > 0) return true;
        return false;
    }

}
