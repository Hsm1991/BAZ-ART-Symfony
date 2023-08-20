<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\EditFormBack;
use App\Form\SendMailForm;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    protected $security;

    public function __construct()
    {
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher,CategoryRepository $categoryRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ));

            $category = $form->get('category')->getData();

            if ($category == 'super admin'){
                $roles = ['ROLE_SUPER_ADMIN'];
            }
            elseif ($category == 'prestataire'){
                $roles = ['ROLE_PRESTATAIRE'];
            }
            else if ($category == 'artiste') {
                $roles = ['ROLE_ARTISTE'];
            }
            else{
                $roles = ['ROLE_USER'];
            }

            $user->setRoles($roles);
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'] , requirements: ['id'=>'\d+'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/recherche', name: 'app_user_search', methods: ['GET'])]
    public function recherche(Request $req, EntityManagerInterface $entityManager)
    {

        $data = $req->get('search');
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->findBy(['firstname' => $data]);


        return $this->render('user/index.html.twig', [
            'users' => $user
        ]);
    }


    #[Route('/{id}/block', name: 'app_user_block')]
    public function blockUser(Request $request, User $user, UserRepository $userRepository, $id): void
    {

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {

//            $user->setStatus('Blocked');
//

            //     $this->denyAccessUnlessGranted('ROLE_ADMIN');   grants access to method , only to admin


        }
    }


    public  function getCurrentUser(Request $request,UserRepository $userRepository){

        $email = $request->getSession()->get(Security::LAST_USERNAME) ;

        $user = $userRepository->findOneBy(['email'=>$email]);

        return $user;
    }


    #[Route('/{id}/change-status', name: 'app_user_change_status')]
    public function changeStatusUser(User $user, UserRepository $userRepository, SendMailService $mail): Response
    {


        if ($user->getStatus()=='Blocked')
        {
            $user->setStatus('Actif');
            $userRepository->save($user, true);
            $status = ' We are glad to inform you that your account has been activated again .
        You can now access our app and benefit from our services .';

            $context = compact('status');

            $mail->send(
                'no-reply@bazart.tn',
                $user->getEmail(),
                'Account Re-activated',
                '/user/warning-status.html.twig',
                $context
            );

        }

        else
        {
            $user->setStatus('Blocked');
            $userRepository->save($user, true);


            $status = ' We are sorry to inform you that your account has been blocked .
        You will no longer be able to benefit from our services until later notice .';


            $context = compact('status');
            $mail->send(
                'no-reply@bazart.tn',
                $user->getEmail(),
                'Account Blocked',
                '/user/warning-status.html.twig',
                $context,
            );

        }
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


//    #[Route('/delete-current-back', name: 'app_user_delete_current_back')]
//    public function deleteCurrent( UserRepository $userRepository ): Response
//    {
//
////        $user_email = $this->getUser()->getUserIdentifier();
////        $user = $userRepository->findOneByEmail($user_email);
//
//
//        $userRepository->remove($this->getUser(), true);
//
//        $this->addFlash('warning', 'Account deleted');
//        return $this->render('security/login.html.twig');
//    }
//
//    #[Route(path: '/modify-current-back', name: 'app_modify_current_back' )]
//    public function modifyCurrent(Request $request,UserRepository $userRepository): Response
//    {
//        // usually you'll want to make sure the user is authenticated first
////        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//
//        $user_email = $this->getUser()->getUserIdentifier();
//        $user = $userRepository->findOneByEmail($user_email);
//
//
//        $form = $this->createForm(EditFormBack::class, $this->getUser());
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $userRepository->save($user, true);
//
//            return $this->render('home/index.html.twig');
//        }
//
//
//        return $this->render('user/edit.html.twig',[
//            'user' => $user,'form' => $form->createView(),
//        ]);
//
//    }



}
