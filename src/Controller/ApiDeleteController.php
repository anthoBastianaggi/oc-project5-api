<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Portfolio;
use App\Entity\Service;
use App\Entity\Skill;

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
    */
    public function deleteService(Service $service)
    {
        $this->em->remove($service);
        $this->em->flush();
        
        $res = [
            'status' => 200,
            'message' => 'Le service a bien été supprimé'
        ];

        return new JsonResponse($res);
    }

    /**
    * @Route("/api/skills/{id}", name="delete_skill", methods={"DELETE"})
    */
    public function deleteSkill(Skill $skill)
    {
        $this->em->remove($skill);
        $this->em->flush();
        
        $res = [
            'status' => 200,
            'message' => 'La compétence a bien été supprimé'
        ];

        return new JsonResponse($res);
    }

    /**
    * @Route("/api/portfolio/{id}", name="delete_portfolio", methods={"DELETE"})
    */
    public function deletePortfolio(Portfolio $portfolio)
    {
        $this->em->remove($portfolio);
        $this->em->flush();

        $res = [
            'status' => 200,
            'message' => 'Le portfolio a bien été supprimé'
        ];

        return new JsonResponse($res);
    }
}
