<?php
class Authentication {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function isAuthorized(){
        //compare request token to db token
        $headers = array_change_key_case(getallheaders(),CASE_LOWER);
        return $this->getToken() === $headers['authorization'];
    }
    private function getToken() {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    
        try {
            $username = $headers['x-auth-user'];
            $stmt = $this->pdo->prepare("SELECT token FROM accounts_tbl WHERE username = ?");
            $stmt->execute([$username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result && isset($result['token'])) {
                return $result['token'];
            }
            return null;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return "";
    }
    private function generateHeader(){
        $header = [
            "typ" => "JWT",
            "alg" => "HS256",
            "app" => "lib",
            "dev" => "Curt D. Mayuga"
        ];
        return base64_encode(json_encode($header));
    }
    private function generatePayload($id, $username){
        $payload = [
            "uid" => $id,
            "uc" => $username,
            "email" => "curtdigma05@gordoncollege.edu.ph",
            "date" => date_create(),
            "exp" => date("Y-m-d H:i:s")
        ];
        return base64_encode(json_encode($payload));
    }
    private function generateToken($id, $username){
        $header = $this->generateHeader();
        $payload = $this->generatePayload($id, $username);
        $signature = hash_hmac("sha256", "$header.$payload", TOKEN_KEY);
        return "$header.$payload." . base64_encode($signature);
    }
    // Encrypt password using bcrypt
    private function encryptPassword($password) {
        $hashFormat = "$2y$10$"; //blowfish
        // bcrypt hash with cost factor 10
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Verify password
    private function isSamePassword($inputPassword, $existingHash) {
        return password_verify($inputPassword, $existingHash);
    }

    // User login function
    public function login($body) {
        $username = $body['username'];
        $password = $body['password'];
        $response = ["code" => 401, "message" => "Invalid credentials."];
    
        try {
            $sql = "SELECT id, username, password, id FROM accounts_tbl WHERE username=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username]);
    
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                if ($this->isSamePassword($password, $user['password'])) {
                    // Generate token using the provided snippet
                    $token = $this->generateToken($user['id'], $user['username']);
                    $token_arr = explode('.', $token);
    
                    // Save token in the database
                    $this->saveToken($token_arr[2], $user['username']);
    
                    // Create payload to return in the response
                    $payload = [
                        "id" => $user['id'],
                        "username" => $user['username'],
                        "token" => $token_arr[2]
                    ];
    
                    $response = [
                        "code" => 200,
                        "message" => "Login successful.",
                        "data" => $payload
                    ];
                }
            }
        } catch (\PDOException $e) {
            $response["message"] = $e->getMessage();
        }
        return $response;
    }
    

    // Register a new user account
    public function addAccount($body) {
        // Encrypt the password using bcrypt
        $password = $this->encryptPassword($body['password']);  
        
        try {
            // Insert the new user into the database
            $sql = "INSERT INTO accounts_tbl (username, password) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$body['username'], $password]);
    
            // Get the ID of the newly inserted user
            $userId = $this->pdo->lastInsertId();
            $username = $body['username'];
    
            // Generate a token for the new user
            $token = $this->generateToken($userId, $username);
            $token_arr = explode('.', $token);
    
            // Save the token to the database
            $this->saveToken($token_arr[2], $username);
    
            return [
                "code" => 200,
                "message" => "Account created successfully.",
                "data" => [
                    "username" => $username,
                    "token" => $token_arr[2]
                ]
            ];
        } catch (\PDOException $e) {
            return ["code" => 400, "message" => $e->getMessage()];
        }
    }

    // Save the login token to the database
    private function saveToken($token, $username) {
        $sql = "UPDATE accounts_tbl SET token=? WHERE username=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token, $username]);
    }

    // Update user details
    public function updateUser($body, $userId) {
        try {
            $updates = [];
            $params = [];

            if (isset($body->username)) {
                $updates[] = "username = ?";
                $params[] = $body->username;
            }

            if (isset($body->password)) {
                $updates[] = "password = ?";
                $params[] = $this->encryptPassword($body->password);  // Encrypt new password
            }

            if (empty($updates)) {
                return ["code" => 400, "message" => "No valid fields to update"];
            }

            $updatesQuery = implode(", ", $updates);
            $sql = "UPDATE accounts_tbl SET $updatesQuery WHERE id = ?";
            $params[] = $userId;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return ["code" => 200, "message" => "User updated successfully"];
        } catch (\PDOException $e) {
            return ["code" => 400, "message" => $e->getMessage()];
        }
    }
}
?>