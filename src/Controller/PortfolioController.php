<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Portfolio;
use App\Form\PortfolioType;
use App\Repository\CategoryPortfolioRepository;
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
            $data = json_decode($request->getContent(), true);
            $portfolio = new Portfolio();
            $form = $this->createForm(PortfolioType::class, $portfolio);   

            if ($request->isMethod('POST')) {
                $form->submit($data);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->em->persist($portfolio);
                    $this->em->flush();
                } else {
                    return $this->json([
                        'status' => 400,
                        'message' => $form->getErrors()
                    ], 400);
                }

                return $this->json([
                    'status' => 201,
                    'message' => 'The project has been created.'
                ], 201);
            }

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
    public function allProjectAction(PortfolioRepository $portfolioRepository)
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
     * @Route("/api/portfolio/{id}", name="portfolio_show", methods={"GET"})
     */
    public function projectAction(PortfolioRepository $portfolioRepository, $id)
    {
        try {
            $portfolio = $portfolioRepository->find($id);
            $data = $this->serialize->serialize($portfolio, 'json');
           
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
    public function projectUpdate(Request $request, Portfolio $portfolio, CategoryPortfolioRepository $categoryPortfolioRepository)
    {
        try {
            $data = json_decode($request->getContent(), true);

            if(!empty($data['category'])) {
                $data['category'] = $categoryPortfolioRepository->find($data['category']);
            }  
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $portfolio->$setter($value);
                }
            }
    
            $errors = $this->validator->validate($portfolio);
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