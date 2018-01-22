<?php

class post {

    private $id;
    private $authorID;
    private $abstract;
    private $fileName;
    private $publicated;
    private $state;
    private $title;

    function post($row = null) {
        if ($row != null) {
            if (sizeof($row) == 7) {
                $this->id = $row['ID'];
                $this->authorID = $row['AUTHOR_ID'];
                $this->abstract = $row['ABSTRACT'];
                $this->fileName = $row['FILENAME'];
                $this->publicated = $row['PUBLICATED'];
                $this->state = $row['STATE'];
                $this->title = $row['TITLE'];
            }
        }
    }

    function getID() {
        return $this->id;
    }

    function setID($_id) {
        $this->id = $_id;
    }

    function getAuthorID() {
        return $this->authorID;
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

    function setAbstract($_abstract) {
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }


}