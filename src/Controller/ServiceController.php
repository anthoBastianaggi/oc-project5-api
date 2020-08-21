<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Service;
use App\Repository\ServiceRepository;

class ServiceController extends AbstractController
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
     * @Route("/api/services/{id}", name="service_update", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function serviceUpdate(Request $request, Service $service)
    {
        try {
            $data = json_decode($request->getContent());
    
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $service->$setter($value);
                }
            }
            
            $errors = $this->validator->validate($service);
            if(count($errors)) {
                $errors = $this->serialize->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
    
            $this->em->flush();
    
            return $this->json([
                'status' => 200,
                'message' => 'The service has been updated.'
            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }        
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
}
