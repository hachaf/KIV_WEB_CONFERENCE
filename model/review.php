<?php

class review {

    private $id;
    private $authorID;
    private $postID;
    private $text;
    private $verdict;
    private $locked;
    private $publicated;

    function review($row = null) {
        if ($row != null) {
            if (sizeof($row) == 7) {
                $this->id = $row['ID'];
                $this->authorID = $row['AUTHOR_ID'];
                $this->postID = $row['POST_ID'];
                $this->text = $row['TEXT'];
                $this->verdict = $row['VERDICT'];
                $this->locked = $row['LOCKED'];
                $this->publicated = $row['PUBLICATED'];
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

    function getPostID() {
        return $this->postID;
    }

    function setPostID($_postID) {
        $this->postID= $_postID;
    }

    function getText() {
        return $this->text;
    }

    function setText($_text) {
        $this->text = $_text;
    }

    function getVerdict() {
        return $this->verdict;
    }

    function setVerdict($_verdict) {
        $this->verdict = $_verdict;
    }

    function getLocked() {
        return $this->locked;
    }

    function setLocked($_locked) {
        $this->locked = $_locked;
    }

    function getPublicated() {
        return $this->publicated;
    }

    function setPublicated($_publicated) {
        $this->publicated = $_publicated;
    }

}