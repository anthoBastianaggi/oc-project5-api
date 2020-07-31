<?php

namespace App\Controller;

use App\Entity\CategoryPortfolio;
use App\Repository\CategoryPortfolioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryPortfolioController extends AbstractController
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
    * @Route("/api/categoryPortfolio", name="category_portfolio_store", methods={"POST"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function categoryPortfolioStore(Request $request)
    {
        try {
            $data = $request->getContent();
            $categoryPortfolio = $this->serialize->deserialize($data, CategoryPortfolio::class, 'json');
            $errors = $this->validator->validate($categoryPortfolio);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
    
            $this->em->persist($categoryPortfolio);
            $this->em->flush();

            return $this->json([
                'status' => 201,
                'message' => 'The category portfolio has been created.'
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }   
    }

     /**
     * @Route("/api/categoryPortfolio", name="category_portfolio_list", methods={"GET"})
     */
    public function categoryPortfolioAction(CategoryPortfolioRepository $categoryPortfolioRepository)
    {
        try {
            $categoryPortfolio = $categoryPortfolioRepository->findAll();
            $data = $this->serialize->serialize($categoryPortfolio, 'json');
           
            return new JsonResponse($data, 200, [], true);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }          
    }

     /**
     * @Route("/api/categoryPortfolio/{id}", name="category_portfolio_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function categoryPortfolioUpdate(Request $request, CategoryPortfolio $categoryPortfolio)
    {
        try {
            $categoryPortfolioUpdate = $this->em->getRepository(CategoryPortfolio::class)->find($categoryPortfolio->getId());
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $categoryPortfolioUpdate->$setter($value);
                }
            }
    
            $errors = $this->validator->validate($categoryPortfolioUpdate);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
           
            return $this->json([
                'status' => 200,
                'message' => 'The category project has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }

     /**
    * @Route("/api/categoryPortfolio/{id}", name="delete_category_portfolio", methods={"DELETE"})
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteCategoryPortfolio(CategoryPortfolio $categoryPortfolio)
    {
        try {
            $this->em->remove($categoryPortfolio);
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The category project has been deleted.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
    }
}