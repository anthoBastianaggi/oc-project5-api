<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Contact;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
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
     * @Route("/api/contact", name="contact_store", methods={"POST"})
     */
    public function contactStore(MailerInterface $mailer, Request $request)
    {
        try {
            $data = $request->getContent();
            $contact = $this->serialize->deserialize($data, Contact::class, 'json');
            $errors = $this->validator->validate($contact);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $this->em->persist($contact);
            $this->em->flush();

            $email = (new Email())
                ->from('a.bastianaggi@gmail.com')
                ->to($contact->getEmail())
                ->subject($contact->getSubject())
                ->text($contact->getContent());
            
            $mailer->send($email);

            return $this->json([
                'status' => 201,
                'message' => 'Your message has been sent.'
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }   
    }
}
