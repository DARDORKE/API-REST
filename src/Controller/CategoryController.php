<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="create_category", methods={"POST"})
     */
    public function createCategory(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): Response
    {
        // Récupération du contenu de la requête.
        $data = json_decode($request->getContent(), true);
        // Création d'une nouvelle catégorie en utilisant le contenu de la requête.
        $category = new Category();
        $category->setTitle($data['title']);
        $category->setContent($data['content']);
        $category->setPublished($data['published']);

        // équivalence avec le composant serializer
        // $category = $serializer->deserialize($request->getContent(), Category::class, 'json');

        // enregistrement de la catégorie en base de données.
        $entityManager->persist($category);
        $entityManager->flush();

        // Utilisation du composant Serializer pour serialiser les données en JSON.
        // Dans le cas d'entités peu complexes (sans relation par exemple)
        // la fonction json_encode peut aussi être utilisée.
        $json = $serializer->serialize($category, 'json');

        // Envoie de la réponse contenant une représentation de l'article en JSON,
        // un code HTTP 201 et un header indiquant le type de contenu en JSON.
        $response = new Response($json);
        $response->setStatusCode(201);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/categories/{id}", name="delete_category", methods={"DELETE"})
     */
    public function deleteCategory(
        $id,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
    ): Response
    {
        $category = $categoryRepository->find($id);

        if (is_null($category)) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $response = new Response(null);
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * @Route("/categories/{id}", name="update_category", methods={"PUT"})
     */
    public function updateCategory(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        CategoryRepository $categoryRepository,
    ): Response
    {
        $category = $categoryRepository->find($id);

        if (is_null($category)) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $data = json_decode($request->getContent(), true);

        $category->setTitle($data['title']);
        $category->setContent($data['content']);
        $category->setPublished($data['published']);

        $entityManager->persist($category);
        $entityManager->flush();

        $json = $serializer->serialize($category, 'json');

        $response = new Response($json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}