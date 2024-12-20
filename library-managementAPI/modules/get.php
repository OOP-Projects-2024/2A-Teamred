<?php
include_once "Common.php";

class Get extends Common {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getaccounts($id = null) {
        $condition = "isdeleted = 0";
        if ($id !== null) {
            $condition .= " AND id=" . $id;
        }

        $result = $this->getDataByTable('accounts_tbl', $condition, $this->pdo);
        if ($result['code'] == 200) {
            return $this->generateResponse($result['data'], "success", "Successfully retrieved records.", $result['code']);
        }
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }
    public function getLogs($date) {
        $filename = "./logs/" . $date . ".log";
        $logs = array();

        try {
            $file = new SplFileObject($filename);
            while (!$file->eof()) {
                array_push($logs, $file->fgets());
            }
            $remarks = "success";
            $message = "Successfully retrieved logs.";
        } catch (Exception $e) {
            $remarks = "failed";
            $message = $e->getMessage();
        }
        return $this->generateResponse(array("logs" => $logs), $remarks, $message, 200);
    }

    public function getBooks($id = null) {
        $sqlString = "SELECT * FROM books_tbl";
        if ($id) {
            $sqlString .= " WHERE id = ?";
        }

        try {
            $stmt = $this->pdo->prepare($sqlString);
            if ($id) {
                $stmt->execute([$id]);
            } else {
                $stmt->execute();
            }

            $data = $stmt->fetchAll();
            return array("code" => 200, "data" => $data);
        } catch (\PDOException $e) {
            return array("code" => 401, "errmsg" => $e->getMessage());
        }
    }
}
?>
