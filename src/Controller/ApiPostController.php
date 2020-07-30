<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Portfolio;
use JMS\Serializer\SerializerInterface;
use App\Entity\Service;
use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiPostController extends AbstractController
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
     * @Route("/api/services", name="service_store", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function serviceStore(Request $request)
    {
        try {
            $data = $request->getContent();
            $service = $this->serialize->deserialize($data, Service::class, 'json');
            $errors = $this->validator->validate($service);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
    
            $this->em->persist($service);
            $this->em->flush();

            return $this->json([
                'status' => 201,
                'message' => 'The service has been created.'
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }   
    }

    /**
     * @Route("/api/skills", name="skill_store", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function skillStore(Request $request)
    {
        try {
            $data = $request->getContent();
            dd($data);
            $skill = $this->serialize->deserialize($data, Skill::class, 'json');
            $errors = $this->validator->validate($skill);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
    
            $this->em->persist($skill);
            $this->em->flush();
    
            return $this->json([
                'status' => 201,
                'message' => 'The skill has been created.'
            ], 201);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }    
    }

    /**
     * @Route("/api/portfolio", name="project_store", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function projectStore(Request $request)
    {
        try {
            $data = $request->getContent();
            $portfolio = $this->serialize->deserialize($data, Portfolio::class, 'json');
            $errors = $this->validator->validate($portfolio);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
            
            $this->em->persist($portfolio);
            $this->em->flush();
    
            return $this->json([
                'status' => 201,
                'message' => 'The project has been created.'
            ], 201);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }       
    }
}
