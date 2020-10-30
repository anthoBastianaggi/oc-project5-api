<?php

namespace App\Controller;

use App\Entity\CategorySkills;
use App\Repository\CategorySkillsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CategorySkillsController extends AbstractController
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
    * @Route("/api/categorySkills", name="category_skills_store", methods={"POST"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function categorySkillsStore(Request $request)
    {
        try {
            $data = $request->getContent();
            $categorySkills = $this->serialize->deserialize($data, CategorySkills::class, 'json');
            $errors = $this->validator->validate($categorySkills);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
    
            $this->em->persist($categorySkills);
            $this->em->flush();

            return $this->json([
                'status' => 201,
                'message' => 'The category skills has been created.'
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }   
    }

    /**
    * @Route("/api/categorySkills", name="category_skills_list", methods={"GET"})
    */
    public function categorySkillsAction(CategorySkillsRepository $categorySkillsRepository)
    {
        try {
            $categorySkills = $categorySkillsRepository->findAll();
            $data = $this->serialize->serialize($categorySkills, 'json');
           
            return new JsonResponse($data, 200, [], true);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }          
    }

    /**
    * @Route("/api/categorySkills/{id}", name="category_skills_update", methods={"PUT"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function categorySkillsUpdate(Request $request, CategorySkills $categorySkills)
    {
        try {
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $categorySkills->$setter($value);
                }
            }
    
            $errors = $this->validator->validate($categorySkills);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
           
            return $this->json([
                'status' => 200,
                'message' => 'The category skills has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }

    /**
    * @Route("/api/categorySkills/{id}", name="delete_category_skills", methods={"DELETE"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteCategorySkills(CategorySkills $categorySkills)
    {
        try {
            $this->em->remove($categorySkills);
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The category skills has been deleted.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}