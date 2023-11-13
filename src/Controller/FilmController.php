<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Get as OAG;
use OpenApi\Annotations\Post as OAP;
use OpenApi\Annotations\Put as OAPu;
use OpenApi\Annotations\Delete as OAD;

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
    #[Route('api/film/list', name: 'app_film_listing', methods: ['GET'])]
    #[OAG\Get(
        path: 'api/film/list',
        summary: 'Liste des films',
        tags: ['Film'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Retourne une liste de films',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Film::class, groups: ['film']))
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Aucun film trouvé'
            )
        ]
    )]
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
    #[Route('api/film/{id}', name: 'get_film', methods: ['GET'])]
    #[OAG\Get(
        path: 'api/film/{id}',
        summary: 'Mettre à jours un film',
        tags: ['Film'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Retourne le film spécifié',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Film::class, groups: ['film']))
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Aucun film trouvé'
            )
        ]
    )]
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
    #[Route('api/film', name: 'create_film', methods: ['POST'])]
    #[OAP\Post(
        path: 'api/film',
        summary: 'Créer un film',
        tags: ['Film'],
        requestBody: new OA\RequestBody(
            description: 'Données du film à créer',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Film::class, groups: ['film']))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Film créé avec succès',
                content: new OA\JsonContent(ref: new Model(type: Film::class, groups: ['film']))
            ),
            new OA\Response(
                response: 400,
                description: 'Données invalides'
            )
        ]
    )]
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
    #[Route('api/film/{id}', name: 'update_film', methods: ['PUT'])]
    #[OAPu\Put(
        path: 'api/film/{id}',
        summary: 'Mettre à jour un film',
        tags: ['Film'],
        requestBody: new OA\RequestBody(
            description: 'Données mises à jour pour le film',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Film::class, groups: ['film']))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Film mis à jour avec succès',
                content: new OA\JsonContent(ref: new Model(type: Film::class, groups: ['film']))
            ),
            new OA\Response(
                response: 404,
                description: 'Film non trouvé'
            )
        ]
    )]
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
    #[Route('api/film/{id}', name: 'delete_film', methods: ['DELETE'])]
    #[OAD\Delete(
        path: 'api/film/{id}',
        summary: 'Supprimer un film',
        tags: ['Film'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Film supprimé avec succès'
            ),
            new OA\Response(
                response: 404,
                description: 'Film non trouvé'
            )
        ]
    )]
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
