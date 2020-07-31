<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Portfolio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Repository\PortfolioRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PortfolioController extends AbstractController
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
