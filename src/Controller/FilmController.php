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
    
    #[Route('/movies', name: 'movies_listing')]
    public function list(): Response
    {
        return $this->json(['movies' => []]);
    }

    #[Route('/movies/{id}', name: 'get_movie')]
    public function getMovie(int $id): Response
    {
        return $this->json(['movies' => "Movie $id"]);
    }

    #[Route('/movies/{id}', name: 'edit_movie')]
    public function editMovie(int $id): Response
    {
        return $this->json(['created_movie' => "Movie $id"]);
    }

    #[Route('/movies/{id}', name: 'delete_movie')]
    public function deleteMovie(int $id): Response
    {
        return $this->json(['deleted_movie' => "Movie $id"], 204);
    }
}
