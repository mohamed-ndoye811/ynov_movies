<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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


use App\Entity\Movie;

class MovieController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // This route is for the index page of the MovieController
    #[Route('api/movies/test', name: 'app_movie', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a welcome message",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Movie::class, groups={"movie"}))
     *     )
     * )
     * @OA\Tag(name="Movie")
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->getMethod() !== 'GET') {
            return $this->json(['message' => 'Method not allowed'], 405);
        }

        return $this->json(['message' => 'Welcome to your new controller!']);
    }

    // This route is for getting a list of all movies
    #[Route('api/movies', name: 'app_movie_listing', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of movies",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Movie::class, groups={"movie"}))
     *     )
     * )
     * @OA\Tag(name="Movie")
     */
    public function list(SerializerInterface $serializer, Request $request): Response
    {
        $movies = $this->entityManager->getRepository(Movie::class)->findAllMovies(
            $request->query->get('page', 1),
            $request->query->get('pageSize', 10)
        );

        return $this->apiResponse($serializer, $movies, $request->getAcceptableContentTypes(), '200',
            ['movie']);
    }

    // This route is for getting a specific movie by ID
    #[Route('api/movies/{id}', name: 'get_movie', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a movie by ID",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Movie::class, groups={"movie"}))
     *     )
     * )
     * @OA\Tag(name="Movie")
     */
    public function getMovie(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $movie = $this->entityManager->getRepository(Movie::class)->find($id);

        if (!$movie) {
            return $this->json(['message' => 'Movie not found'], 404);
        }

        return $this->apiResponse($serializer, ['movie' => $movie], $request->getAcceptableContentTypes()[0], 200,
            ['movie']);
    }

    // This route is for creating a new movie
    #[Route('api/movies', name: 'create_movie', methods: ['POST'])]
    /**
     * @OA\RequestBody(
     *    description="Données du movie à créer",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"movie"}))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Movie créé avec succès",
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"movie"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Le champ 'nom' est manquant"
     * )
     * @OA\Response(
     *     response=409,
     *     description="Le movie existe déjà"
     * )
     * @OA\Tag(name="Movie")
     */
    public function createMovie(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $movieData = json_decode($request->getContent(), true);

        // Check required fields
        $requiredFields = ['nom', 'dateDeParution'];
        foreach ($requiredFields as $field) {
            if (!isset($movieData[$field])) {
                return $this->json(['message' => "The field '$field' is missing"], 400);
            }
        }

        // Check date format
        $dateDeParution = \DateTime::createFromFormat('Y-m-d', $movieData['dateDeParution']);
        if (!$dateDeParution) {
            return $this->json(['message' => "Invalid date format for 'dateDeParution'. Use 'Y-m-d' format."], 400);
        }

        // Check if movie already exists
        $dbMovie = $entityManager->getRepository(Movie::class)->findBy(["nom" => $movieData['nom']]);
        if($dbMovie) {
            return $this->json(['message' => "The movie '" . $movieData['nom'] . "' already exists!"], 409);
        }

        // Create new movie
        $movie = $serializer->deserialize($request->getContent(), Movie::class, 'json');
        $movie->setDateDeParution($dateDeParution);

        $entityManager->persist($movie);
        $entityManager->flush();

        return $this->apiResponse($serializer, ['message' => 'Movie created successfully', 'movie' => $movie], $request->getAcceptableContentTypes()[0], 201,
            ['movie']);
    }

    // This route is for editing an existing movie by ID
    #[Route('api/movies/{id}', name: 'update_movie', methods: ['PUT'])]
    /**
     * @OA\RequestBody(
     *    description="Données du movie à mettre à jour",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"movie"}))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Movie mis à jour avec succès",
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"movie"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Movie non trouvé"
     * )
     * @OA\Tag(name="Movie")
     */
    public function updateMovie(int $id, Request $request, SerializerInterface $serializer): Response
    {
        $movie = $this->entityManager->getRepository(Movie::class)->find($id);
        if (!$movie) {
            return $this->json(['message' => 'Movie not found'], 404);
        }

        $movieData = json_decode($request->getContent(), true);

        // Check required fields
        $requiredFields = ['nom', 'dateDeParution'];
        foreach ($requiredFields as $field) {
            if (!isset($movieData[$field])) {
                return $this->json(['message' => "The field '$field' is missing"], 400);
            }
        }

        // Check date format
        $dateDeParution = \DateTime::createFromFormat('Y-m-d', $movieData['dateDeParution']);
        if (!$dateDeParution) {
            return $this->json(['message' => "Invalid date format for 'dateDeParution'. Use 'Y-m-d' format."], 400);
        }

        $movie->setNom($movieData['nom']);
        $movie->setDescription($movieData['description'] ?? null);
        $movie->setDateDeParution($dateDeParution);
        $movie->setNote($movieData['note'] ?? null);

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $this->apiResponse($serializer, ['message' => 'Movie updated successfully'] , $request->getAcceptableContentTypes()[0], 200,
            ['movie']);
    }

    // This route is for deleting an existing movie by ID
    #[Route('api/movies/{id}', name: 'delete_movie', methods: ['DELETE'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Movie supprimé avec succès",
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"movie"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Movie non trouvé"
     * )
     * @OA\Tag(name="Movie")
     */
    public function deleteMovie(int $id, SerializerInterface $serializer, Request $request): Response
    {
        $movie = $this->entityManager->getRepository(Movie::class)->find($id);

        if ($movie) {
            $this->entityManager->remove($movie);
            $this->entityManager->flush();
            $message = 'Movie deleted successfully';
            $statusCode = 200;
        } else {
            $message = 'Movie not found';
            $statusCode = 404;
        }

        return $this->apiResponse($serializer, ['message' => $message], $request->getAcceptableContentTypes()[0], $statusCode,
            ['movie']);
    }

    // This route is for searching for a movie by title or description
    #[Route('api/movies/search/{searchTerm}', name: 'search_movie', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of movies matching the search term",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Movie::class, groups={"movie"}))
     *     )
     * )
     * @OA\Tag(name="Movie")
     */
    public function searchMovie(string $searchTerm, SerializerInterface $serializer, Request $request): Response
    {
        $movies = $this->entityManager->getRepository(Movie::class)->findByTitleOrDescription($searchTerm);

        if (!$movies) {
            return $this->json(['message' => 'No movie found'], 404);
        }

        return $this->apiResponse($serializer, ['movies' => $movies], $request->getAcceptableContentTypes()[0], 200,
            ['movie']);
    }

    // This route is for upload a poster for a movie
    #[Route('api/movies/{id}/upload', name: 'upload_movie_poster', methods: ['POST'])]
    /**
     * @OA\RequestBody(
     *    description="Poster du movie à mettre à jour",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="poster",
     *                 description="Poster du movie",
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Poster mis à jour avec succès",
     *     @OA\JsonContent(ref=@Model(type=Movie::class, groups={"movie"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Movie non trouvé"
     * )
     * @OA\Tag(name="Movie")
     */
    public function uploadMoviePoster(int $id, Request $request, SerializerInterface $serializer): Response {
        $movie = $this->entityManager->getRepository(Movie::class)->find($id);

        if (!$movie) {
            return $this->json(['message' => 'Movie not found'], 404);
        }

        $poster = $request->files->get('poster');

        if (!$poster) {
            return $this->json(['message' => 'Poster not found'], 404);
        }

        $originalFilename = pathinfo($poster->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename.'-'.uniqid().'.'.$poster->guessExtension();

        try {
            $poster->move($this->getParameter('posters_directory'), $newFilename);
        } catch (FileException $e) {
            return $this->json(['message' => 'Failed to upload file'], 500);
        }

        $movie->setImage($newFilename);
        $this->entityManager->flush();

        return $this->apiResponse($serializer, ['movie' => $movie], $request->getAcceptableContentTypes()[0], 200,
            ['movie']);
    }

    // this function is to return a response in JSON or XML format
    public function apiResponse(SerializerInterface $serializer, $data, $format, $statusCode, $groups = null): Response
    {
        $context = SerializationContext::create()->setGroups($groups);
        $contentType = $format == 'application/xml' ? 'application/xml' : 'application/json';
        $format = $contentType == 'application/xml' ? 'xml' : 'json';

        $responseContent = $serializer->serialize($data, $format, $context);
        $response = new Response($responseContent, $statusCode);
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }
}
