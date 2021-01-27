<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use App\Model\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/reset/password", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')){
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);

            if ($user){
                // 1. Enregistre en base la demande de reset_password avec user, token , createdAt.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // 2 : Envoie d'un email à l'utilisateur avec un lien lui permettant de mettre à jour son mot de passe.

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);
                $content = "Hello". $user->getFirstname() . "<br> You ask to update your password on eChopper.<br/>";
                $content .= "<a href='$url'>Please click on this link !</a>";

                $mail = new Mail();
                $mail->Send($user->getEmail(), $user->getFirstname().''. $user->getLastname(), 'Forget password !', $content);

                $this->addFlash('notice', 'You going to receive an email with the confirmation of your request');

            }else{
                $this->addFlash('notice', 'Email unknown !');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route ("/modify_password/{token}", name="update_password")
     */
    public function update(Request $request,$token, UserPasswordEncoderInterface  $encoder){
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);

        if (!$reset_password){
            return $this->redirectToRoute('reset_password');
        }
        else
            {

            // Vérification si createdAt = now - 3h
            $now = new \DateTime();


            if ($now < $reset_password->getCreatedAt()->modify('+ 3hour'))
            {
                // Modifier le mot de passe
                $this->addFlash('notice', 'You request has expired !');
                return $this->redirectToRoute('reset_password');
            }

            // Return d'une vue avec la confirmation
            // Encodage MDP - Flush Database
            // Redirect User > Login Page

            $form = $this->createForm(ResetPassword::class);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()){
                $new_pwd = $form->get('new_password')->getData();

                // Encodage
                $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);
                $reset_password->getUser()->setPassword($password);

                // Flush BDD
                $this->entityManager->flush();

                // Redirection
                $this->addFlash('notice', 'Well done ! You password has been updated');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('reset_password/update.html.twig',[
               'form' => $form->createView()
            ]);
        }
    }
}
