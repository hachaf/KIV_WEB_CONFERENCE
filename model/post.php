<?php

class post {

    private $id;
    private $authorID;
    private $abstract;
    private $fileName;
    private $publicated;
    private $state;

    function post($row) {
        if (sizeof($row) == 6) {
            $this->id = $row['ID'];
            $this->authorID = $row['AUTHOR_ID'];
            $this->abstract = $row['ABSTRACT'];
            $this->fileName = $row['FILENAME'];
            $this->publicated = $row['PUBLICATED'];
            $this->state = $row['STATE'];
        }
    }

    function getID() {
        return $this->id;
    }

    function setID($_id) {
        $this->id = $_id;
    }

    function getAuthorID() {
        return $this->id;
    }

    function setAuthorID($_authorID) {
        $this->authorID = $_authorID;
    }

    function getFilename() {
        return $this->fileName;
    }

    function setFilename($_filename) {
        $this->fileName = $_filename;
    }

    function getAbstract() {
        return $this->abstract;
    }

    function setAbctract($_abstract) {
        $this->abstract = $_abstract;
    }

    function getPublicated() {
        return $this->publicated;
    }

    function setPublicated($_publicated) {
        $this->publicated = $_publicated;
    }

    function getState() {
        return $this->state;
    }

    function setState($_state) {
        $this->state = $_state;
    }


}