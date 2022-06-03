<?php

namespace App\Controller;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Entity\Comment;
use App\Form\PostFormType;
use App\Form\Ticket\TicketFormType;
use App\Repository\CommentRepository;
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
    public function open($id, PostRepository $postRepo,  PaginatorInterface $paginator, Request $request, CommentRepository $commnetrepo){
        $post = $postRepo->findOneById($id);
        $qb = $commnetrepo->getWithSearchQueryBuilder($id);
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('Forum/post.html.twig',[
            'post' => $post,
            'pagination' => $pagination
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
     * @Route("/forum/{id}/delete}", name="app_forum_delete")
     * @IsGranted("ROLE_GESTOR")
     */
    public function delete($id, PostRepository $postrepo, EntityManagerInterface $em){
        $em->remove($postrepo->findOneById($id));
        $em->flush();
        $Route = $this->generateUrl('app_forum_list', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
        return $this->redirect($Route);
    }
    /**
     * @Route("/forum/{idp}/delete/{id}", name="app_forum_delete_comment")
     * @IsGranted("ROLE_GESTOR")
     */
    public function deleteComment($idp,$id, CommentRepository $commentrepo, EntityManagerInterface $em){
        $em->remove($commentrepo->findOneById($id));
        $em->flush();
        $Route = $this->generateUrl('app_forum_open', ['id'=>$idp], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
        return $this->redirect($Route);
    }
    /**
     * @Route("/forum/{id}/comment}", name="app_forum_comment")
     * @IsGranted("ROLE_GESTOR")
     */
    public function comment($id, PostRepository $postRepository, Request $request, EntityManagerInterface $em){
        $post = $postRepository->findOneById($id);
        $commenttext = $request->request->get('comment');
        $comment = new Comment();
        $comment->setContent($commenttext);
        $comment->setCreatedBy($this->getUser()->getUserIdentifier());
        $post->addComment($comment);
        $em->persist($comment);
        $em->persist($post);
        $em->flush();
        $Route = $this->generateUrl('app_forum_open', ['id'=>$post->getId()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
        return $this->redirect($Route);
    }
    /**
     * @Route("/forum/{id}/vote}", name="app_forum_vote")
     * @IsGranted("ROLE_GESTOR")
     */
    public function vote($id, PostRepository $postRepository, Request $request, EntityManagerInterface $em){
        $post = $postRepository->findOneById($id);
        $direction = $request->request->get('direction');

        if ($direction === "up"){
            $post->setScore($post->getScore()+1);
            $votado = $post->getVotado();
            $votado[] = $this->getUser()->getUserIdentifier();
            $post->setVotado($votado);
        }
        else if ($direction === "down"){
            $post->setScore($post->getScore()-1);
            $votado = $post->getVotado();
            $votado[] = $this->getUser()->getUserIdentifier();
            $post->setVotado($votado);
        }
        $em->persist($post);
        $em->flush();
        $Route = $this->generateUrl('app_forum_open', ['id'=>$post->getId()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
        return $this->redirect($Route);
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
