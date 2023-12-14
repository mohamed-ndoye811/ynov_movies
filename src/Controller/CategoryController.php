<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Serializer\SerializerInterface;
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

        return $this->apiResponse($serializer, ["categories" => $categories], $request->getAcceptableContentTypes()[0], 200, ['category', 'film:read']);
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
