<?php
class MySQLConnection
{
    public $connection = null;

    public function __construct($database_address, $database_username, $database_password, $database_name)
    {
        $this->$connection = new mysqli($database_address, $database_username, $database_password, $database_name);
        if ($connection->connect_error) {
            die('Connection failed: '.$connection->connect_error);
        }
    }

    public function __destruct()
    {
        $this->$connection->close();
    }

    public function query($query)
    {
        return $this->$connection->query($query);
    }
}
