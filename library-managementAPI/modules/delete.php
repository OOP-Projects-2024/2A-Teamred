<?php
class Delete {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function deleteBook($id) {
        try {
            $sqlString = "DELETE FROM books_tbl WHERE id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$id]);

            return array("code" => 400, "data" => null);
        } catch (\PDOException $e) {
            return array("code" => 401, "errmsg" => $e->getMessage());
        }
    }
}
?>