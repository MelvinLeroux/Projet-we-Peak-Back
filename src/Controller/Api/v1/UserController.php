<?php

namespace App\Controller\Api\v1;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Services\Base64FileExtractor;

#[Route('/api/v1/users', name: 'app_api_v1_user')]
class UserController extends AbstractController
{

        private UserPasswordHasherInterface $hasher;
        private SluggerInterface $slugger;
        private UserService $userService;
        private EmailVerifier $emailVerifier;
        private Base64FileExtractor $base64FileExtractor;
    public function __construct( UserPasswordHasherInterface $hasher,  SluggerInterface $slugger,  UserService $userService, EmailVerifier $emailVerifier,Base64FileExtractor $base64FileExtractor,
    )
        
    {
        $this->hasher = $hasher;
        $this->userService = $userService;
        $this->emailVerifier = $emailVerifier;
        $this->base64FileExtractor = $base64FileExtractor;
    
    }

    #[Route('', name: 'list',  methods:"GET")]
    #[IsGranted('ROLE_USER')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findAll();
        return $this->json(
            $user,
            200,
            [],
            ['groups' => ['user.list']]
        );
    }

    #[Route('/{id}', name: 'show',  methods:"GET")]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): JsonResponse
    {
        return $this->json(
            $user,
            200,
            [],
            ['groups' => ['user.show']]
        );
    }

    #[Route('', name: 'create',  methods:"POST")]
    public function create(
        Request $request, 
        #[MapRequestPayload(serializationContext:['groups' => ['user.create']])]
        User $user,
        EntityManagerInterface $entityManager, UserRepository $userRepository
    ): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);   
            $user = $this->userService->createUser($data, $entityManager, $userRepository);
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                return new JsonResponse('L\'adresse e-mail n\'est pas au bon format');
            };
            $entityManager->persist($user);
            $entityManager->flush();
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('wepeakfrance@gmail.com', 'support'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig'));
            
            return $this->json(
            $user,
            201,
            [],
            ['groups' => ['user.show']]
        );
        
        } catch (Error $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }
        
    } 
    

    #[Route('/{id}', name: 'update',  methods:["PATCH"])]
    public function update(int $id, User $user, Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        // Récupérer l'utilisateur actuel
        /** @var \App\Entity\User */
        $currentUser = $security->getUser();
        $currentUserID = $currentUser->getId();
        $currentUserRoles = $currentUser->getRoles();
        // Vérifier si l'utilisateur actuel est bien celui dont l'ID correspond à l'ID fourni dans l'URL ou s'il est admin
        if ($currentUserID !== $id && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de modifier ce profil.'], JsonResponse::HTTP_FORBIDDEN);
        }
        
        $data = json_decode($request->getContent(), true);

        $user = $this->userService->update($user, $data, $entityManager, $this->base64FileExtractor , $this->hasher);

        $entityManager->flush();

        return $this->json(
            $user,
            200, 
            [], 
            ['groups' => ['user.show']]
        );
    }

    #[Route('/{id}', name: 'delete',  methods:"DELETE")]
    public function delete(int $id, User $user, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $currentUser = $security->getUser();
        $currentUserID = $currentUser->getId();
        $currentUserRoles = $currentUser->getRoles();
        // Vérifier si l'utilisateur actuel est bien celui dont l'ID correspond à l'ID fourni dans l'URL ou s'il est admin
        if ($currentUserID !== $id && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
        
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de modifier ce profil.'], JsonResponse::HTTP_FORBIDDEN);
    }
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse("utilisateur effacé avec succès", JsonResponse::HTTP_ACCEPTED);
    }
}

