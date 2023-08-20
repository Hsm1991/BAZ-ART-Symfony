<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditFormFront;
use App\Form\RegisterFrontType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Security\AppAuthenticatorFront;
use App\Security\EmailVerifier;
use App\service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/front')]
class FrontController extends AbstractController
{


  private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
{
    $this->emailVerifier = $emailVerifier;
}




    #[Route('/home-front', name: 'app_cnx_front')]
    public function cnxfront(): Response
    {
//        $flashy->success('A Verification Email has been sent');

        return $this->render('front/index.html.twig');

    }



    #[Route('/main', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/main-front.html.twig');
    }


    #[Route('/login', name: 'app_login_front')]
    public function loginFront(AuthenticationUtils $authenticationUtils, AppAuthenticatorFront $authenticator, Request $Request): Response
    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('app_front');
//
//        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    #[Route(path: '/logout', name: 'app_logout_front')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login_front');

    }

    #[Route(path: '/front-01', name: 'app_01_front')]
    public function first(): Response
    {

        return $this->render('front/index.html.twig');

    }







    #[Route('/oubli-pass', name:'forgotten_password_front')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());
//            $user = $usersRepository->findOneBy([$form->get('email')->getData()]);
            // On vérifie si on a un utilisateur


            if($user){
                // On génère un token de réinitialisation

                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_pass_front', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // On crée les données du mail
                $context = compact('url', 'user');

                // Envoi du mail
                $mail->send(
                    'no-reply@e-commerce.fr',
                    $user->getEmail(),
                    'Reset de mot de passe',
                    '/front/password_reset.html.twig',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login_front');
            }
            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login_front');
        }

        return $this->renderForm('front/reset_password_request.html.twig', [
            'requestPassForm' => $form
        ]);
    }



    #[Route('/oubli-pass/{token}', name:'reset_pass_front')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {


        // On vérifie si on a ce token dans la base
        $user = $usersRepository->findOneByResetToken($token);

        if($user){

            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // On efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login_front');
            }

            return $this->renderForm('front/reset_password.html.twig', [
                'passForm' => $form,
            ]);
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login_front');
    }





    #[Route('/register', name: 'app_register_front')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFrontType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

//            $category = $form->get('category')->getData();


            $roles = ['ROLE_USER'];

            $user->setRoles($roles);
            $entityManager->persist($user);
            $entityManager->flush();

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            try {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email_front', $user,
                    (new TemplatedEmail())
                        ->from(new Address('bazartfront@demo.com', 'demo bot'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('front/confirmation_email.html.twig')
                );
            } catch (TransportExceptionInterface $e) {
            }
            // do anything else you need here, like send an email
            $this->addFlash("success", "Inscription réussie !");


//            return $userAuthenticator->authenticateUser(
//                $user,
//                $authenticator,
//                $request
//            );
            return $this->redirectToRoute('app_login_front');
        }

        return $this->render('front/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email-front', name: 'app_verify_email_front')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register_front');
        }


        return $this->redirectToRoute('app_home_front');

    }

    #[Route(path: '/modify', name: 'app_modify_front' )]
    public function modify(Request $request,UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {
       // usually you'll want to make sure the user is authenticated first
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user_email = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneByEmail($user_email);


        $form = $this->createForm(EditFormFront::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_event_index_front');
        }


        return $this->render('front/modify.html.twig',[
            'user' => $user,'form' => $form->createView(),
        ]);

    }


//    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
//
//    }


//    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
//    public function delete(Request $request, User $user, UserRepository $userRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
//            $userRepository->remove($user, true);
//        }
//
//        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//    }


    #[Route('/delete', name: 'app_user_delete_front')]
    public function delete( UserRepository $userRepository ): Response
    {
          $msg=' account deleted';
        $user_email = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneByEmail($user_email);


            $userRepository->remove($user, true);

        $this->addFlash('warning', 'Account deleted');
        return $this->render('front/main-front.html.twig',[
            'msg' => $msg
        ]);
    }




}
