<?php
class Patch {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function patchBooks($body, $id) {
        try {
            $sqlString = "UPDATE books_tbl SET title = ?, author = ?, genre = ?, release_date = ?, available = ?, quantity = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([
                $body->title,
                $body->author,
                $body->genre,
                $body->release_date,
                $body->available,
                $body->quantity,
                $id
            ]);

            return array("code" => 400, "data" => null);
        } catch (\PDOException $e) {
            return array("code" => 401, "errmsg" => $e->getMessage());
        }
    }
}
?>