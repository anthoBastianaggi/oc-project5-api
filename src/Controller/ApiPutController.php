<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Entity\Service;
use App\Entity\Skill;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ApiPutController extends AbstractController
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
     * @Route("/api/users/{id}", name="users_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function userUpdate(Request $request, User $user)
    {
        try {
            $userUpdate = $this->em->getRepository(User::class)->find($user->getId());
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $userUpdate->$setter($value);
                }
            }
    
            $errors = $this->validator->validate($userUpdate);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The user has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    /**
     * @Route("/api/services/{id}", name="service_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function serviceUpdate(Request $request, Service $service)
    {
        try {
            $serviceUpdate = $this->em->getRepository(Service::class)->find($service->getId());
            $data = json_decode($request->getContent());
    
            dd($data);
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $serviceUpdate->$setter($value);
                }
            }
            
            $errors = $this->validator->validate($serviceUpdate);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The service has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }

    /**
     * @Route("/api/skills/{id}", name="skill_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function skillUpdate(Request $request, Skill $skill)
    {
        try {
            $skillUpdate = $this->em->getRepository(Skill::class)->find($skill->getId());
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $skillUpdate->$setter($value);
                }
            }
    
            $errors = $this->validator->validate($skillUpdate);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
            
            return $this->json([
                'status' => 200,
                'message' => 'The skill has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }  
    }

     /**
     * @Route("/api/portfolio/{id}", name="project_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function projectUpdate(Request $request, Portfolio $portfolio)
    {
        try {
            $portfolioUpdate = $this->em->getRepository(Portfolio::class)->find($portfolio->getId());
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $portfolioUpdate->$setter($value);
                }
            }
    
            $errors = $this->validator->validate($portfolioUpdate);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
           
            return $this->json([
                'status' => 200,
                'message' => 'The project has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}
