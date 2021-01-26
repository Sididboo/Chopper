<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Model\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if (!$search_email){
                $password = $encoder->encodePassword($user,$user->getPassword());

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mail = new Mail();
                $content = "Hello". $user->getFirstName()."<br>Mauris pellentesque rutrum diam, et ullamcorper sapien sagittis quis. Donec ipsum dui, suscipit ut faucibus at, iaculis sed massa. Nunc tempus libero enim, ut cursus tortor pellentesque euismod";
                $mail->Send($user->getEmail(), $user->getFirstName(), 'Welcome on eChopper', $content);

                $notification = "Well done for your inscription";
            }else{
                $notification = "Email already exist !";
            }


        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
