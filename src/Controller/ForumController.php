<?php

namespace App\Controller;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Form\PostFormType;
use App\Form\Ticket\TicketFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/forum/list", name="app_forum_list")
     */
    public function list(PostRepository $postRepo, PaginatorInterface $paginator, Request $request){
        $qb = $postRepo->getWithSearchQueryBuilder("prueba");
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        dd($pagination);

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
                $post = $form->getData();
                $em->persist($post);
                $em->flush();
                $Route = $this->generateUrl('app_forum', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
            return $this->redirect($Route);
        }
        return $this->render('Forum/postform.html.twig',[
            'postform' => $form->createView()
        ]);
    }
}
