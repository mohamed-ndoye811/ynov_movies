<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Film;

class FilmController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // This route is for the index page of the FilmController
    #[Route('/film', name: 'app_film', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        // If the request method is not GET, return an error message with status code 405
        if ($request->getMethod() !== 'GET') {
            return $this->json(['error' => 'Invalid request method'], 405);
        }
        // Return a welcome message with the path of the controller
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FilmController.php',
        ]);
    }

    // This route is for getting a list of all movies
    #[Route('/film/list', name: 'app_film_listing', methods: ['GET'])]
    public function list(SerializerInterface $serializer, Request $request): Response
    {
        $films = $this->entityManager->getRepository(Film::class)->findAllFilms();
        $format = $request->getAcceptableContentTypes();

        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize($films, 'xml', ['groups' => 'film']);
            $contentType = 'application/xml';
        } else {
            // Default to JSON
            $responseContent = $serializer->serialize($films, 'json', ['groups' => 'film']);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    // This route is for getting a specific movie by ID
    #[Route('/film/{id}', name: 'get_film', methods: ['GET'])]
    public function getFilm(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);
        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }

        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize($film, 'xml', ['groups' => 'film']);
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize($film, 'json', ['groups' => 'film']);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    // This route is for creating a new movie
    #[Route('/film', name: 'create_film', methods: ['POST'])]
    public function createFilm(Request $request, SerializerInterface $serializer): Response
    {
        $film = $serializer->deserialize($request->getContent(), Film::class, 'json');
        $this->entityManager->persist($film);
        $this->entityManager->flush();

        $responseData = [
            'message' => 'Film created successfully',
            'film' => $film
        ];

        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize($responseData, 'xml', ['groups' => 'film']);
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize($responseData, 'json', ['groups' => 'film']);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent, 201);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    // This route is for editing an existing movie by ID
    #[Route('/film/{id}', name: 'update_film', methods: ['PUT'])]
    public function updateFilm(int $id, Request $request, SerializerInterface $serializer): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);
        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }

        $serializer->deserialize($request->getContent(), Film::class, 'json', ['object_to_populate' => $film]);
        $this->entityManager->flush();

        $responseData = [
            'message' => 'Film updated successfully',
            'film' => $film
        ];

        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize($responseData, 'xml', ['groups' => 'film']);
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize($responseData, 'json', ['groups' => 'film']);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent, 200);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    // This route is for deleting an existing movie by ID
    #[Route('/film/{id}', name: 'delete_film', methods: ['DELETE'])]
    public function deleteFilm(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);
        if (!$film) {
            $responseContent = ['message' => 'Film not found'];
            $statusCode = 404;
        } else {
            $this->entityManager->remove($film);
            $this->entityManager->flush();
            $responseContent = ['message' => 'Film deleted successfully'];
            $statusCode = 200;
        }

        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize($responseContent, 'xml');
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize($responseContent, 'json');
            $contentType = 'application/json';
        }

        $response = new Response($responseContent, $statusCode);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

}
