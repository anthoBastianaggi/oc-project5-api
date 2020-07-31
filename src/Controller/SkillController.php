<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Skill;
use App\Repository\SkillRepository;

class SkillController extends AbstractController
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
     * @Route("/api/skills", name="skill_store", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function skillStore(Request $request)
    {
        try {
            $data = $request->getContent();
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
     * @Route("/api/skills", name="skill_list", methods={"GET"})
     */
    public function skillAction(SkillRepository $skillRepository)
    {
        try {
            $skills = $skillRepository->findAll();
            $data = $this->serialize->serialize($skills, 'json', SerializationContext::create()->setGroups(array('list_skill', 'cat_skill')));
    
            return new JsonResponse($data, 200, [], true);
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
    * @Route("/api/skills/{id}", name="delete_skill", methods={"DELETE"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteSkill(Skill $skill)
    {
        try {
            $this->em->remove($skill);
            $this->em->flush();
            
            return $this->json([
                'status' => 200,
                'message' => 'The skill has been deleted.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}
