<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->serialize = $serializer;
        $this->validator = $validator;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     */
    public function register(Request $request)
    {
        try {
            $values = json_decode($request->getContent());
            
            if(isset($values->username,$values->password)) {
                $user = new User();
                $user->setUsername($values->username);
                $user->setPassword($this->passwordEncoder->encodePassword($user, $values->password));
                $user->setRoles(['ROLE_ADMIN']);
                $errors = $this->validator->validate($user);

                if(count($errors)) {
                    $errors = $this->serialize->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                }

                $this->em->persist($user);
                $this->em->flush();
    
                $data = [
                    'status' => 201,
                    'message' => 'The user has been created.'
                ];
    
                return new JsonResponse($data, 201);
            }
            $data = [
                'status' => 500,
                'message' => 'You must enter the username and password keys.'
            ];
            return new JsonResponse($data, 500);

        } catch(UniqueConstraintViolationException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/login", name="login", methods={"POST"})
     */
    public function login()
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }
}
