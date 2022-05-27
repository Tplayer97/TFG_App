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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="app_forum")
     *      * @IsGranted("ROLE_GESTOR")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ForumController.php',
        ]);
    }
    /**
     * @Route("/forum/open/{id}", name="app_forum_open")
     */
    public function open($id, PostRepository $postRepo,  PaginatorInterface $paginator){
        $post = $postRepo->findOneById($id);
        return $this->render('Forum/post.html.twig',[
            'post' => $post,
        ]);
    }
    /**
     * @Route("/forum/list", name="app_forum_list")
     *      * @IsGranted("ROLE_GESTOR")
     */
    public function list(PostRepository $postRepo, PaginatorInterface $paginator, Request $request){
        $Posts = $postRepo->findAll();
        $qb = $postRepo->getWithSearchQueryBuilder();
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
    return $this->render('Forum/list.html.twig',[
        'pagination' => $pagination,
        'posts' => $Posts
    ]);
    }
    /**
     * @Route("/forum/createpost", name="app_forum_create")
     * @IsGranted("ROLE_GESTOR")
     */
    public function CrearPost(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            {
                $post = $form->getData();
                $post->setCreatedBy($this->getUser()->getUserIdentifier());
                $em->persist($post);
                $em->flush();
                $Route = $this->generateUrl('app_forum_list', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
            return $this->redirect($Route);
        }
        return $this->render('Forum/postform.html.twig',[
            'postform' => $form->createView()
        ]);
    }
}
