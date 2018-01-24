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
        $query = "select * from conuser;";
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
        $query = "select * from conuser where TYPE = 'REV';";
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
        $query = "select * from conuser where id = $id;";
        $statement = $this->connection->prepare($query);
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

        $pars = array();
        $pars["LOGIN"] = $user->getLogin();
        $pars["PASSWORD"] = $user->getPassword();
        $pars["TYPE"] = $user->getType();
        if ($user->getBlocked()) {
            $pars["BLOCKED"] = "1";
        } else {
            $pars["BLOCKED"] = "0";
        };

        // SLOZIT TEXT STATEMENTU s otaznikama
        $insert_columns = "";
        $insert_values  = "";

        if ($pars != null)
            foreach ($pars as $column => $value) {
                // pridat carky
                if ($insert_columns != "") $insert_columns .= ", ";
                if ($insert_columns != "") $insert_values .= ", ";

                $insert_columns .= "`$column`";
                $insert_values .= "?";
            }

        $stmt_text = "insert into `conuser` ($insert_columns) values ($insert_values);";
        $stmt = $this->connection->prepare($stmt_text);

        $bind_param_number = 1;

        foreach ($pars as $column => $value) {
            $stmt->bindValue($bind_param_number, $value);  // vzdy musim dat value, abych si nesparoval promennou (to nechci)
            $bind_param_number ++;
        }

        $stmt->execute();

        $item_id = $this->connection->lastInsertId();
        $user->setID($item_id);
        return $user;
    }

    function update($user) {
        if ($user == null) return null;

        $pars = array();
        $pars["LOGIN"] = "'" . $user->getLogin() . "'";
        $pars["PASSWORD"] = "'" . $user->getPassword() . "'";
        $pars["TYPE"] = "'" . $user->getType() . "'";
        if ($user->getBlocked()) {
            $pars["BLOCKED"] = "1";
        } else {
            $pars["BLOCKED"] = "0";
        }
        $pars["ID"] = $user->getID();

        // SLOZIT TEXT STATEMENTU s otaznikama
        $insert_columns = "";
        $insert_values  = "";

        foreach ($pars as $column => $value) {
            // pridat carky
            if ($insert_columns != "") $insert_columns .= ", ";
            if ($insert_columns != "") $insert_values .= ", ";

            $insert_columns .= "`$column`";
            $insert_values .= "?";
        }

        $stmt_text = 'UPDATE CONUSER SET LOGIN = '
            . $pars["LOGIN"]
            . ', PASSWORD = ' . $pars["PASSWORD"]
            . ', TYPE = ' . $pars["TYPE"]
            . ', BLOCKED = ' . $pars["BLOCKED"] .' WHERE ID = ' .  $pars["ID"] . ';';

        $stmt = $this->connection->prepare($stmt_text);

        $bind_param_number = 1;

        foreach ($pars as $column => $value) {
            $stmt->bindValue($bind_param_number, $value);
            $bind_param_number ++;
        }

        $stmt->execute();
        return $user;
    }

    function remove($id) {
        if (!is_int($id)) return null;
        $stmt_text = 'DELETE FROM CONUSER WHERE ID = ' . $id . ';';
        $stmt = $this->connection->prepare($stmt_text);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    function getByNameAndPwd($username, $pwd) {
        $stmt = $this->connection->prepare("select * from conuser where (login=:login and password=:password)");
        $stmt->bindParam(':login', $username);
        $stmt->bindParam(':password', $pwd);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res == null) return null;
        return new conUser($res);
    }

    function userExist($username) {
        $stmt = $this->connection->prepare("select count(1) from conuser where (login=:login)");
        $stmt->bindParam(':login', $username);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_COLUMN, 0);
        if ($res > 0) return true;
        return false;
    }

}
