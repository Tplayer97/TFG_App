<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\FileUploadFormType;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;



class ModerationController extends AbstractController
{
    /**
     * @Route("/moderation", name="app_moderation")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ModerationController.php',
        ]);
    }

    /**
     * @Route("/moderation/download/{id}", name="app_moderation_download")
     */
    public function download($id, ContentRepository $contentRepository){
        $asset= $contentRepository->findOneById($id)->getFile();
        $response = new BinaryFileResponse($asset);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,substr($asset, 8));
        return $response;
    }
    /**
     * @Route("/moderation/vote/{id}", name="app_moderation_vote")
     */
    public function vote($id, Request $request, ContentRepository $contentRepository, EntityManagerInterface $em){
        $direction = $request->request->get('direction');
        $content = $contentRepository->findOneById($id);
        if ($direction === "up"){
            $content->setScore($content->getScore()+1);
        }
        else if ($direction === "down"){
            $content->setScore($content->getScore()-1);

        }
        $em->persist($content);
        $em->flush();
        $Route = $this->generateUrl('app_moderation_moderate');
        return $this->redirect($Route);
    }

    /**
     * @Route("/moderation/moderate", name="app_moderation_moderate")
     */
    public function moderate(ContentRepository $contentrepo){
        $content = $contentrepo->findByNoScore()[0];
        if ($content){
            return $this->render('moderation/moderate.html.twig',[
                'content' => $content
            ]);
        }
        else {
            $Route = $this->generateUrl('app_forum_list', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
            return $this->redirect($Route);
        }
    }


    /**
     * @Route("/moderation/create", name="app_moderation_create")
     */
    public function create(Request $request, EntityManagerInterface $em){
        $form = $this->createForm(FileUploadFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form['File']->getData();
            if ($uploadedFile){
                $content = new Content();

                $newFile = self::upload($uploadedFile);

                $content->setFile($newFile['path']);
                $content->setTitle($form['Title']->getData());
                $content->setCreatedBy($this->getUser()->getUserIdentifier());

             $em->persist($content);
             $em->flush();
                $Route = $this->generateUrl('app_forum_list', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
                return $this->redirect($Route);
            }
        }
        return $this->render('moderation/create.html.twig',[
            'contentform' => $form->createView()
        ]);
    }

    public function upload(UploadedFile $uploadedFile): array
    {
        $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $mimeType = pathinfo($uploadedFile->getClientMimeType(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalName).".".$uploadedFile->guessExtension();
        $name = uniqid().$uploadedFile->getClientOriginalName();
        $uploadedFile->move('uploads/',$name);
        return [
            'mimeType' => $mimeType,
            'newFilename' => $newFilename,
            'path' => 'uploads/'.$name
        ];
    }
}
