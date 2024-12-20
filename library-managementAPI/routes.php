<?php
require_once "./config/database.php";
require_once "./modules/Get.php";
require_once "./modules/Post.php";
require_once "./modules/Patch.php";
require_once "./modules/Delete.php";
require_once "./modules/auth.php";  // Include the auth module
require_once "./modules/Crypt.php";

$db = new Connection();
$pdo = $db->connect();

$get = new Get($pdo);
$post = new Post($pdo);
$patch = new Patch($pdo);
$delete = new Delete($pdo);
$auth = new Authentication($pdo);  // Create an instance of the Authentication class
$crypt = new Crypt();

if (isset($_REQUEST['request'])) {
    $request = explode("/", $_REQUEST['request']);
} else {
    echo "URL does not exist.";
}

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if ($auth->isAuthorized()) {  // Check if the user is authorized
            switch ($request[0]) {
               
                
                case "books":  // Handle the "students" endpoint
                   $dataString = json_encode($get->getBooks($request[1] ?? null));
                   echo $crypt->encryptData ($dataString);
                    break;

    
                    case "log":
                        echo json_encode($get->getLogs($request[1] ?? date("Y-m-d")));
                        break;

                default:
                    http_response_code(404);
                    echo "Endpoint not found.";
            }
        } else {
            http_response_code(401);
            echo "Unauthorized access.";
        }
        break;
    

    case "POST":
        
        $body = json_decode(file_get_contents("php://input"), true);  // Check if the user is authorized
            switch ($request[0]) {
                case "postBooks":
                    echo json_encode($post->postBooks($body));
                break;
                case "register":
                    echo json_encode($auth->addAccount($body));
                break;
                case "login":
                    echo json_encode($auth->login($body));
                break;
                case "decrypt":
                    echo $crypt->decryptData($body);
                    
                break;
                default:
                http_response_code(404);
                echo "Endpoint not found.";

        }
   
       
    break;



    case "PATCH":
        if ($request[0] === "books") {
            $body = json_decode(file_get_contents("php://input"));
            echo json_encode($patch->patchBooks($body, $request[1]));
        }
        break;

    case "DELETE":
        if ($request[0] === "books") {
            echo json_encode($delete->deleteBook($request[1]));
        }
        break;

    default:
        http_response_code(400);
        echo "Invalid Request Method.";
        break;
}


$pdo = null;
?>