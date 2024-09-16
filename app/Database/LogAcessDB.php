<?php

namespace App\Database;

use PDO;
use PDOException;

class LogAcessDB {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct() {
        try {
            $this->host     = getenv('database.default.hostname');
            $this->dbname   = getenv('database.default.database');
            $this->username = getenv('database.default.username');
            $this->password = getenv('database.default.password');
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }

    public function savePhpError(array $data) {
        try {
            $sql = "INSERT INTO log_access (uuid, created_at, id_log_query, success, ip_address, request_method, request_endpoint, status_code, code, request_json_body, response_json_body) VALUES (:uuid, :created_at, :id_log_query, :success, :ip_address, :request_method, :request_endpoint, :status_code, :code, :request_json_body, :response_json_body)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':uuid', $data['uuid']);
            $stmt->bindParam(':created_at', $data['created_at']);
            $stmt->bindParam(':id_log_query', $data['id_log_query']);
            $stmt->bindParam(':success', $data['success']);
            $stmt->bindParam(':ip_address', $data['ip_address']);
            $stmt->bindParam(':request_method', $data['request_method']);
            $stmt->bindParam(':request_endpoint', $data['request_endpoint']);
            $stmt->bindParam(':status_code', $data['status_code']);
            $stmt->bindParam(':code', $data['code']);
            $stmt->bindParam(':request_json_body', $data['request_json_body']);
            $stmt->bindParam(':response_json_body', $data['response_json_body']);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
            exit();
        }
    }
}
?>
