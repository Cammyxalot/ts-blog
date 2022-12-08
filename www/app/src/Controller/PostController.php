<?php

namespace App\Controller;

use App\Model\Factory\PDOFactory;
use App\Model\Repository\PostRepository;
use App\Model\Repository\UserRepository;
use App\Route\Route;
use App\Model\Entity\Post;

class PostController extends Controller
{
    #[Route('/', 'homePage', ['GET'])]
    public function home($error = [])
    {
        $postRepository = new PostRepository(new PDOFactory());
        $posts = $postRepository->getAllPost();
        if($posts) {
            echo json_encode($posts);
            exit();
        }
        echo json_encode(["error" => "pas de post"]);
        die;
    }

    #[Route('/post', 'newPost', ['POST'])]
    public function newPost()
    {
        $headers = getallheaders();
        $token = $headers['authorization'];

        $userRepository = new UserRepository(new PDOFactory());
        $user = $userRepository->getUserByToken($token);
        $author = $user->getUsername();
        $userId = $user->getId();

        $args = [...$_POST, 'author' => $author, 'user_id' => $userId];
        $postRepository = new PostRepository(new PDOFactory());
        $post = new Post($args);
        $post = $postRepository->insert($post);
        echo json_encode($post);
        http_response_code(200);
        exit();
    }

    #[Route('/post/delete', 'deletePost', ['POST'])]
    public function deletePost()
    {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: authorization");
        header("Content-Type: application/json");

        $json = file_get_contents("php://input");
        $body = json_decode($json, true);
        $postId = $body['id'];
        if ($postId) {
            $postRepository = new PostRepository(new PDOFactory());
            $postRepository->delete($postId);
            http_response_code(200);
        }
        exit();
    }

    #[Route('/post/patch', 'patchPost', ['POST'])]
    public function patchPost()
    {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: authorization");
        header("Content-Type: application/json");

        $json = file_get_contents("php://input");
        $postRepository = new PostRepository(new PDOFactory());
        $post = new Post(json_decode($json, true));
        $postRepository->update($post);
        http_response_code(200);
        exit();
    }


}
