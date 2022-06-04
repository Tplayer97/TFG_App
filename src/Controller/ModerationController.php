<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\FileUploadFormType;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;



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
                $content->setTitle($form['Title']);
             $em->persist($content);
             $em->flush();
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
        $uploadedFile->move('uploads/',uniqid().$uploadedFile->getClientOriginalName());
        return [
            'mimeType' => $mimeType,
            'newFilename' => $newFilename,
            'path' => 'uploads/'.uniqid().$newFilename
        ];
    }
}
