<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Repository\PortfolioRepository;
use App\Repository\ServiceRepository;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiGetController extends AbstractController
{
    public function __construct(
        SerializerInterface $serializer
    )
    {
        $this->serialize = $serializer;
    }

    /**
     * @Route("/api/users", name="users", methods={"GET"})
     */
    public function userAction(UserRepository $userRepository)
    {
        try {
            $users = $userRepository->findAll();
            $data = $this->serialize->serialize($users, 'json');
           
            return new JsonResponse($data, 200, [], true);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }

     /**
     * @Route("/api/services", name="service_list", methods={"GET"})
     */
    public function serviceAction(ServiceRepository $serviceRepository)
    {
        try {
            $services = $serviceRepository->findAll();
            $data = $this->serialize->serialize($services, 'json');
           
            return new JsonResponse($data, 200, [], true);
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
     * @Route("/api/portfolio", name="project_list", methods={"GET"})
     */
    public function projectAction(PortfolioRepository $portfolioRepository)
    {
        try {
            $portfolio = $portfolioRepository->findAll();
            $data = $this->serialize->serialize($portfolio, 'json', SerializationContext::create()->setGroups(array('list_portfolio', 'cat_portfolio')));
    
            return new JsonResponse($data, 200, [], true);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}
