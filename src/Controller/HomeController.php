<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Home;
use App\Repository\HomeRepository;

class HomeController extends AbstractController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    )
    {
        $this->serialize = $serializer;
        $this->validator = $validator;
        $this->em = $em;
    }

    /**
     * @Route("/api/home", name="home_list", methods={"GET"})
     */
    public function homeAction(HomeRepository $homeRepository)
    {
        try {
            $home = $homeRepository->findAll();
            $data = $this->serialize->serialize($home, 'json');
           
            return new JsonResponse($data, 200, [], true);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }

    /**
     * @Route("/api/home/{id}", name="home_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function homeUpdate(Request $request, Home $home)
    {
        try {
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $home->$setter($value);
                }
            }
            
            $errors = $this->validator->validate($home);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The home has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}
