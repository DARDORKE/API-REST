<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(
        UserRepository $userRepository,
        SerializerInterface $serializer,
    ): Response
    {
        $user = $userRepository->findAll();

        $json = $serializer->serialize($user, 'json');

        $response = new Response($json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/users/{id}', name: 'get_user', methods: ['GET'])]
    public function getUser(
        $id,
        UserRepository $userRepository,
        SerializerInterface $serializer,
    ): Response
    {
        $user = $userRepository->find($id);

        $json = $serializer->serialize($user, 'json');

        $response = new Response($json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setAge($data['age']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setCreatedAt($data['createdAt']);

        $entityManager->persist($user);
        $entityManager->flush();

        $json = $serializer->serialize($user, 'json');

        $response = new Response($json);
        $response->setStatusCode(201);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(
        $id,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $user = $userRepository->find($id);

        if (is_null($user)) {
            throw $this->createNotFoundException("L'utilisateur est introuvable.");
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response(null);
        $response->setStatusCode(204);

        return $response;
    }

    #[Route('/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(
        $id,
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $user = $userRepository->find($id);

        if (is_null($user)) {
            throw $this->createNotFoundException("L'utilisateur est introuvable");
        }

        $data = json_decode($request->getContent(), true);

        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setAge($data['age']);
        $user->setCreatedAt($data['createdAt']);

        $entityManager->persist($user);
        $entityManager->flush();

        $json = $serializer->serialize($user, 'json');

        $response = new Response($json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
