<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Goutte\Client;


class DefaultController extends Controller
{
    public function indexAction()
    {
        $test = "test";
        return $this->render('AppBundle::home.html.twig');
    }

    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $contact->setSenddate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $email = \Swift_Message::newInstance()
                ->setSubject("Message From : " . $contact->getName())
                ->setFrom($contact->getEmail())
                ->setTo('southwestfrancepools@gmail.com')
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'AppBundle:email:contact.html.twig',
                        array('contact' => $contact)
                    )
                );

            if (!$this->get('mailer')->send($email)) {
                $this->addFlash("email_fail", "Failed to send email message");
            } else {
                $this->addFlash("email_success", "Email sent !");
            }
        }

        return $this->render('AppBundle::contact.html.twig', array('form' => $form->createView()));
    }

    public function crawlAction(){
        $client = new Client();
        $crawler = $client->request('GET', 'http://www.sudouest.fr');
        //$crawlerLinks = $crawler->links();
        $test = $crawler->filter('.premium a');
        $crawlerLinks = $test->links();
        foreach($crawlerLinks as $link){
            $uri = $link->getUri();
        }
        return $this->render('AppBundle::crawl.html.twig', array('crawlText' => $crawler->text()));
    }
}

