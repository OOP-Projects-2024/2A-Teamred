<?php
// Set HTTP headers for CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, GET, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-User");
header("Access-Control-Max-Age: 3600");

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-User");
    header("HTTP/1.1 200 OK");
    die();
  }

// Set default timezone
date_default_timezone_set("Asia/Manila");

// Database credentials

define("SERVER", "localhost");
define("DBASE", "library_db");
define("USER", "root");
define("PWORD", "");
define("SECRET_KEY", "curt"); // Your secret key
define("TOKEN_KEY", "12E1561FB866FE9D966538F2125A5");

// Database connection class
class Connection {
    protected $connectionString = "mysql:host=" . SERVER . ";dbname=" . DBASE . ";charset=utf8";
    protected $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,  
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    ];

    public function connect() {
        try {
            return new \PDO($this->connectionString, USER, PWORD, $this->options);
        } catch (\PDOException $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(["error" => $e->getMessage()]);
            exit();
        }
    }
}
?>