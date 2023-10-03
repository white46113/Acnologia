<?php
namespace logia\DatabaseConnection;
use PDO;
interface DatabaseConnectionInterface
{
    public function open();

    public function close();
}