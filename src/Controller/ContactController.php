<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    
    /**
     * @Route("/contact", name="show_contact")
     */
    public function contact(Request $request, EntityManagerInterface $entityManager,  MailerInterface $mailer)
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            $entityManager->persist($contactFormData);
            $entityManager->flush();
            $contactFormDate = $form->getData();
            $getmail = $contactFormDate->getEmail();
            $getmessage = $contactFormDate->getMessage();
 
            $message = (new Email())
 
                ->from($getmail)
                ->to('contact@partage-tesastuces.fr')
                ->subject('Demande de contact')
                ->text('sender : ' . $getmail . \PHP_EOL . $getmessage, 'text/plain');
            $mailer->send($message);
            
            

            $this->addFlash('success',"Vore message a été envoyé");

            return $this->redirectToRoute('show_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

 


}