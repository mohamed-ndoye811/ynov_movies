<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;


use App\Entity\Film;

class FilmController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // This route is for the index page of the FilmController
    #[Route('api/film', name: 'app_film', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a welcome message",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
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
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of films",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film", "category:read"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function list(SerializerInterface $serializer, Request $request): Response
    {
        $context = SerializationContext::create()->setGroups(['film', 'category:read']);
        $page = $request->query->get('page', 1);
        $pageSize = $request->query->get('pageSize', 10);

        $films = $this->entityManager->getRepository(Film::class)->findAllFilms($page, $pageSize);
        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize(["films" => $films], 'xml', $context);
            $contentType = 'application/xml';
        } else {
            // Default to JSON
            $responseContent = $serializer->serialize(["films" => $films], 'json', $context);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    // This route is for getting a specific movie by ID
    #[Route('api/film/{id}', name: 'get_film', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a film by ID",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film", "category:read"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function getFilm(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $film = $this->entityManager->getRepository(Film::class)->find($id);
        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }

        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize(['film' => $film], 'xml',
                ['groups' => ['film', 'category:read']]);
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize(['film' => $film], 'json',
                ['groups' => ['film', 'category:read']]);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }

    // This route is for creating a new movie
    #[Route('api/film', name: 'create_film', methods: ['POST'])]
    /**
     * @OA\RequestBody(
     *    description="Données du film à créer",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Film créé avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Le champ 'nom' est manquant"
     * )
     * @OA\Response(
     *     response=409,
     *     description="Le film existe déjà"
     * )
     * @OA\Tag(name="Film")
     */
    public function createFilm(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $filmData = json_decode($request->getContent());

        if(!isset($filmData?->nom)) {
            return $this->json(['message' => "The field 'nom' is missing"], 400);
        }

        if(!isset($filmData?->dateDeParution)) {
            return $this->json(['message' => "The field 'dateDeParution' is missing"], 400);
        }

        $dbFilm = $entityManager->getRepository(Film::class)->findBy(["nom" => $filmData->nom]);
        if($dbFilm) {
            return $this->json(['message' => "The film '" . $filmData->nom . "' already exists!"], 409);
        }

        $film = $serializer->deserialize($request->getContent(), Film::class, 'json');
        if (!$film) {
            return $this->json(['message' => 'Film not found'], 404);
        }
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
    /**
     * @OA\RequestBody(
     *    description="Données du film à mettre à jour",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Film mis à jour avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Film non trouvé"
     * )
     * @OA\Tag(name="Film")
     */
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
    /**
     * @OA\Response(
     *     response=200,
     *     description="Film supprimé avec succès",
     *     @OA\JsonContent(ref=@Model(type=Film::class, groups={"film"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Film non trouvé"
     * )
     * @OA\Tag(name="Film")
     */
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

    // This route is for searching for a movie by title or description
    #[Route('api/film/search/{searchTerm}', name: 'search_film', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of films matching the search term",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film", "category:read"}))
     *     )
     * )
     * @OA\Tag(name="Film")
     */
    public function searchFilm(string $searchTerm, SerializerInterface $serializer, Request $request): Response
    {
        $films = $this->entityManager->getRepository(Film::class)->findByTitleOrDescription($searchTerm);
        if (!$films) {
            return $this->json(['message' => 'No films found'], 404);
        }

        $format = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize(['films' => $films], 'xml',
                ['groups' => ['film', 'category:read']]);
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize(['films' => $films], 'json',
                ['groups' => ['film', 'category:read']]);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }
}
