<?php
include_once "Common.php";

class Post extends Common {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function postBooks($body) {
        try {
            // Attempt to insert data using the parent class' postData method
            $result = $this->postData("books_tbl", $body, $this->pdo);

            if ($result['code'] == 200) {
                $this->logger("testthunder5", "POST", "Created a new book record");
                return $this->generateResponse($result['data'], "success", "Successfully created a new record.", $result['code']);
            }

            $this->logger("testthunder5", "POST", $result['errmsg']);
            return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
        } catch (\PDOException $e) {
            // Handle any unexpected PDO exceptions
            $this->logger("testthunder5", "POST", $e->getMessage());
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }
}
?>
