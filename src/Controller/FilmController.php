<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'app_film')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FilmController.php',
        ]);
    }

    #[Route('/film/list', name: 'app_film_listing')]
    public function list(): JsonResponse
    {
        return $this->json(['movies' => []]);
    }

    #[Route('/film/list/{id}', name: 'get_film')]
    public function getMovie(int $id): JsonResponse
    {
        return $this->json(['movies' => "Movie $id"]);
    }

    #[Route('/film/list/{id}', name: 'edit_film')]
    public function editMovie(int $id): JsonResponse
    {
        return $this->json(['created_movie' => "Movie $id"]);
    }

    #[Route('/film/list/{id}', name: 'delete_film')]
    public function deleteMovie(int $id): JsonResponse
    {
        return $this->json(['deleted_movie' => "Movie $id"], 204);
    }
}
