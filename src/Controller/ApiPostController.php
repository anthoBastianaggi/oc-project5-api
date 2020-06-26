<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Portfolio;
use JMS\Serializer\SerializerInterface;
use App\Entity\Service;
use App\Entity\Skill;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    
            return new Response('', Response::HTTP_CREATED);
        } catch (RuntimeException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }   
    }

    /**
     * @Route("/api/skills", name="skill_store", methods={"POST"})
     */
    public function skillStore(Request $request)
    {
        try {
            $data = $request->getContent();
            $skill = $this->serialize->deserialize($data, Skill::class, 'json');
    
            $this->em->persist($skill);
            $this->em->flush();
    
            return new Response('', Response::HTTP_CREATED);
        } catch(NotNullConstraintViolationException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }    
    }

    /**
     * @Route("/api/portfolio", name="portfolio_store", methods={"POST"})
     */
    public function portfolioStore(Request $request)
    {
        try {
            $data = $request->getContent();
            $portfolio = $this->serialize->deserialize($data, Portfolio::class, 'json');
            
            $this->em->persist($portfolio);
            $this->em->flush();
    
            return new Response('', Response::HTTP_CREATED);
        } catch(NotNullConstraintViolationException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }       
    }
}
