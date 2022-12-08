<?php

namespace App\Controller;

use App\Model\Factory\PDOFactory;
use App\Model\Repository\UserRepository;
use App\Model\Entity\User;
use App\Route\Route;

class SecurityController extends Controller
{
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test()
    {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: authorization");
        header("Content-Type: application/json");

        echo json_encode("truc");
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $userRepository = new UserRepository(new PDOFactory());
        $user = $userRepository->getUserByName($username);

        if ($user && $user->verifyPassword($password)) {
            /*$_SESSION['user'] = serialize($user);*/
            echo json_encode($user);
            die;
            http_response_code(200);
            exit();
        }
        $this->signUp();
    }

    #[ROUTE('/signUp', 'signup', ['POST'])]
    public function signUp()
    {
        $username = $_POST['username'];

        $userRepository = new UserRepository(new PDOFactory());
        $user = $userRepository->getUserByName($username);
        if (!isset($user)) {
            $bytes = random_bytes(20);
            $token = bin2hex($bytes);
            $args = [...$_POST, 'token' => $token];
            $user = new User($args);
            $user = $userRepository->insert($user);
            echo json_encode($user);
            http_response_code(200);
            exit();
        }
        echo json_encode(['error' => 'Mot de passe ou utilisateur invalid']);
    }

    #[Route('/signOut', 'signout', ['POST'])]
    public function signout()
    {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            unset($_SESSION["user"]);
            http_response_code(200);
        }
        exit();
    }
}
