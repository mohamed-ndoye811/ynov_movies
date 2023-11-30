<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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

        $format = $request->getAcceptableContentTypes();

        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize(["categories" => $categories], 'xml',
                ['groups' => ['category', 'film:read']]);
            $contentType = 'application/xml';
        } else {
            // Default to JSON
            $responseContent = $serializer->serialize(["categories" => $categories], 'json',
                ['groups' => ['category', 'film:read']]);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }
}
