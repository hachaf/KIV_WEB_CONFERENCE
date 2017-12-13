<?php

class conUser {

    private $id;
    private $login;
    private $password;
    private $type;
    private $blocked;

    function conUser($row) {
        if (sizeof($row) == 5) {
            $this->id = $row['ID'];
            $this->login = $row['LOGIN'];
            $this->password = $row['PASSWORD'];
            $this->type = $row['TYPE'];
            $this->blocked = $row['BLOCKED'];
        }
    }

    function setID($_id) {
        $this->id = $_id;
    }

    function getID() {
        return $this->id;
    }

    function setLogin($_login) {
        $this->login = $_login;
    }

    function getLogin() {
        return $this->login;
    }

    function setPassword($_password) {
        $this->password = $_password;
    }

    function getPassword() {
        return $this->password;
    }

    function setType($_type) {
        $this->type = $_type;
    }

    function getType() {
        return $this->type;
    }

    function setBlocked($_blocked) {
        if ($_blocked) {
            $this->blocked = 1;
        } else {
            $this->blocked = 0;
        }
    }

    function getBlocked() {
        return $this->blocked == 1;
    }

}