<?php

namespace App\Controller;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Form\PostFormType;
use App\Form\Ticket\TicketFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="app_forum")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ForumController.php',
        ]);
    }
    /**
     * @Route("/forum/createpost", name="app_forum_create")
     */
    public function CrearPost(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
            {
            $Route = $this->generateUrl('app_forum', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
            return $this->redirect($Route);
        }
        return $this->render('Forum/postform.html.twig',[
            'postform' => $form->createView()
        ]);
    }
}
