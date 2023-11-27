<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('api/category', name: 'app_category', methods: ['GET'])]
    public function index(SerializerInterface $serializer, Request $request): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        $format = $request->getAcceptableContentTypes();

        if (in_array('application/xml', $format)) {
            $responseContent = $serializer->serialize(["categories" => $categories], 'xml', ['groups' => 'category']);
            $contentType = 'application/xml';
        } else {
            // Default to JSON
            $responseContent = $serializer->serialize(["categories" => $categories], 'json', ['groups' => 'category']);
            $contentType = 'application/json';
        }

        $response = new Response($responseContent);
        $response->headers->set('Content-Type', $contentType);
        return $response;
    }
}
