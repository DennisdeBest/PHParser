<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Url;
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

    public function crawlAction($link = "http://www.sudouest.com")
    {
        $client = new Client();
        if(!$crawler = $client->request('GET', $link)){
           return false;
        };
        //$crawlerLinks = $crawler->links();
        $test = $crawler->filter('body a');
        $crawlerLinks = $test->links();
        foreach ($crawlerLinks as $link) {
            $uri = $link->getUri();
            $repo = $this->getDoctrine()->getRepository('AppBundle:Url');
            if ($repo->findOneByLink($uri)) {
                continue;
            }
            $url = new Url();
            $url->setLink($uri);
            $em = $this->getDoctrine()->getManager();
            $em->persist($url);
            $em->flush();
        }
        return true;
    }

    public function scrapAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Url');
        $links = $repo->getUnprocessed();
        $count = 0;
        foreach ($links as $link) {
            if ($count > 1000) {
                break;
            }
            $em = $this->getDoctrine()->getManager();
            if(!$this->crawlAction($link['link'])){
                continue;
            };
            $url = $em->getRepository('AppBundle:Url')->find($link['id']);
            $url->setProcessed(1);
            $em->flush();
            $count++;
        }
    }
}

