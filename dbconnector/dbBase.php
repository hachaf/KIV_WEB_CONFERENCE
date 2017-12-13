<?php

/**
 * Databázová vrstva
 */
class dbBase {
	
	public $connection; 	// tam si ulozim aktualni spojeni
	private $connection_type = 0;
	
	/**
	 * Konstruktor
	 */
    function base() {
        $this->connection_type = DB_CONNECTION_USE_PDO_MYSQL;
    }
	
	/**
	 * Připojí k vybrané databázi dle konstruktoru.
	 */
	function Connect() {
        try {
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
            $this->connection = new PDO("mysql:host=".MYSQL_DATABASE_SERVER.";dbname=".MYSQL_DATABASE_NAME."",
                MYSQL_DATABASE_USER, MYSQL_DATABASE_PASSWORD, $options);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
	}
	
	/**
	 * Odpojí se od vybrané databáze.
	 */
	function Disconnect() {
        $this->connection = null;
	}

}
