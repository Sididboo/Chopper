<?php

namespace App\Controller;

use App\Entity\Order;
use App\Model\Cart;
use App\Model\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager =$entityManager;
    }

    /**
     * @Route("/order/success/{stripeSessionId}", name="order_success")
     */
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if ($order->getState() == 0){
            $cart->remove();

            $order->setState(1);
            $this->entityManager->flush();

            $mail = new Mail();
            $content = "Hello". $order->getUser()->getFirstName()."<br>Mauris pellentesque rutrum diam, et ullamcorper sapien sagittis quis. Donec ipsum dui, suscipit ut faucibus at, iaculis sed massa. Nunc tempus libero enim, ut cursus tortor pellentesque euismod";
            $mail->Send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Confirmation of you order '.$order->getReference(), $content);

        }


        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
