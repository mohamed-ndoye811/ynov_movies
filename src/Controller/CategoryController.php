<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class CategoryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('api/category/list', name: 'app_category', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of categories",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class, groups={"category", "film:read"}))
     *     )
     * )
     * @OA\Tag(name="Category")
     */
    public function index(SerializerInterface $serializer, Request $request): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return $this->apiResponse($serializer, ["categories" => $categories], $request->getAcceptableContentTypes()[0], 200,
            ['category', 'film:read']);
    }

    #[Route('api/category/{name}', name: 'app_category_show', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the category",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class, groups={"category", "film:read"}))
     *     )
     * )
     * @OA\Tag(name="Category")
     */
    public function show(SerializerInterface $serializer, Request $request, $name): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);

        return $this->apiResponse($serializer, ["category" => $category], $request->getAcceptableContentTypes()[0], 200,
            ['category', 'film:read']);
    }

    #[Route('api/category', name: 'create_category', methods: ['POST'])]
    /**
     * @OA\RequestBody(
     *    description="Données de la catégorie à créer",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Catégorie créée avec succès",
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Tag(name="Category")
     */
    public function createCategory(Request $request, SerializerInterface $serializer): Response
    {
        $categoryData = json_decode($request->getContent());

        if(!isset($categoryData?->name)) {
            return $this->json(['message' => "The field 'name' is missing"], 400);
        }

        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');
        if (!$category) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->apiResponse($serializer, ['message' => 'Category created successfully', 'category' => $category], $request->getAcceptableContentTypes()[0], 201,
            ['category', 'film:read']);
    }
    #[Route('api/category/{name}', name: 'update_category', methods: ['PUT'])]
    /**
     * @OA\RequestBody(
     *    description="Données de la catégorie à mettre à jour",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Catégorie mise à jour avec succès",
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Tag(name="Category")
     */
    public function updateCategory(Request $request, SerializerInterface $serializer, $name): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $categoryData = json_decode($request->getContent());

        if(isset($categoryData?->name)) {
            $category->setName($categoryData->name);
        }

        $this->entityManager->flush();

        return $this->apiResponse($serializer, ['message' => 'Category updated successfully', 'category' => $category], $request->getAcceptableContentTypes()[0], 200,
            ['category', 'film:read']);
    }

    #[Route('api/category/{name}', name: 'delete_category', methods: ['DELETE'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Catégorie supprimée avec succès",
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Tag(name="Category")
     */
    public function deleteCategory(Request $request, SerializerInterface $serializer, $name): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json(['message' => 'Category deleted successfully'], 200);
    }

    // this function is to return a response in JSON or XML format
    public function apiResponse(\JMS\Serializer\SerializerInterface $serializer, $data, $format, $statusCode, $groups = null): Response
    {

        $context = SerializationContext::create()->setGroups($groups);

        if ($format == 'application/xml') {
            $responseContent = $serializer->serialize($data, 'xml', $context);
            $contentType = 'application/xml';
        } else {
            // Par défaut, on utilise le JSON
            $responseContent = $serializer->serialize($data, 'json', $context);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent, $statusCode);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }
}
