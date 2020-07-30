<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Portfolio;
use App\Entity\Service;
use App\Entity\Skill;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ApiDeleteController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    /**
    * @Route("/api/services/{id}", name="delete_service", methods={"DELETE"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteService(Service $service)
    {
        try {
            $this->em->remove($service);
            $this->em->flush();
            
            return $this->json([
                'status' => 200,
                'message' => 'The service has been deleted.'
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

    /**
    * @Route("/api/portfolio/{id}", name="delete_project", methods={"DELETE"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteProject(Portfolio $portfolio)
    {
        try {
            $this->em->remove($portfolio);
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The project has been deleted.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}
