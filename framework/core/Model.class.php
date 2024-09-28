<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class Model {

    private $conn;

    protected function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Dynamically determine parameter types
    private function getParamTypes($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob and other types
            }
        }
        return $types;
    }

    protected function select($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        $stmt->close();
        return $rows;
    }

    protected function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
    
            if (!empty($params)) {
                $types = $this->getParamTypes($params);
                $stmt->bind_param($types, ...$params);
            }
    
            $result = $stmt->execute();
            if ($result === false) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }
    
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            // Log the error message
            error_log("Error in execute function: " . $e->getMessage());
    
            // Optionally display the error for debugging (comment this out in production)
            echo "Error in execute function: " . $e->getMessage();
    
            return false; // Indicate failure
        }
    }
    
    
    

    // Start a transaction
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }

    // Commit the transaction
    public function commit() {
        $this->conn->commit();
    }

    // Rollback the transaction
    public function rollback() {
        $this->conn->rollback();
    }

    // Get the last inserted ID
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>
