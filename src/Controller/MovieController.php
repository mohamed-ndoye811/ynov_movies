<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
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