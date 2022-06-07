<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateMoodleUserFormType;
use App\Form\EditUserFormType;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     *  @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {

        return $this->render('admin/main.html.twig', [
            'controller_name' => 'AdminController',
            'user' =>null
        ]);
    }

    /**
     *  @Route("/admin/search", name="app_admin_search")
     */
    public function search(Request $request){
        $name = $request->request->get('search');

        $PDO = new \PDO("mysql:dbname=moodle;host=localhost", "root", "");
        $sql="SELECT * FROM mdl_user WHERE username = '$name'";

        $users = $PDO->query($sql);
        if ($users->rowCount() > 0){
            $user = $users->fetch(2);
        }
        else $user = null;

        return $this->render('admin/main.html.twig',[
            'user' =>$user
        ]);
    }
    /**
     * @Route("/admin/edit/{id}", name="app_admin_edit")
     */
    public function edit($id, Request $request){
        $PDO = new \PDO("mysql:dbname=moodle;host=localhost", "root", "");
        $form = $this->createForm(EditUserFormType::class);
        $PDO = new \PDO("mysql:dbname=moodle;host=localhost", "root", "");
        $sql="SELECT * FROM mdl_user WHERE id = '$id'";

        $users = $PDO->query($sql);
        if ($users->rowCount() > 0){
            $user = $users->fetch(2);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $sql="";

            $PDO->query($sql);
            return $this->render('admin/main.html.twig', [
                'user' => null
            ]);
        }
        return $this->render('admin/edit.html.twig',[
           'editform' =>$form->createView(),
            'user' => $user
        ]);
        }

    /**
     * @Route("/admin/delete/{id}", name="app_admin_delete")
     */
    public function delete($id){

        $PDO = new \PDO("mysql:dbname=moodle;host=localhost", "root", "");
        $sql="DELETE FROM mdl_user WHERE id = '$id'";

        $PDO->query($sql);

        return $this->render('admin/main.html.twig', [
            'user' => null
        ]);
    }
    /**
     * @Route("/admin/create", name="app_admin_create")
     */
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher){

        $form = $this->createForm(CreateMoodleUserFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $username =$form->getData()['username'];

            $rol[] = $form->getData()['roles'];
            $user = new User();
            //dd($user);
            $user->setUsername($username);
            $user->setRole(" ");
            $user->setRoles($rol);
            $password = $passwordHasher->hashPassword($user, $form->getData()['password']);
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            return $this->render('admin/main.html.twig', [
                'user' => null
            ]);
        }
        return $this->render('admin/form.html.twig',[
            'createform' => $form->createView()
        ]);
    }
}
