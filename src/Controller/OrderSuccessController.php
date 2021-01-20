<?php

namespace App\Controller;

use App\Entity\Order;
use App\Model\Cart;
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

        if (!$order->getIsPaid()){
            $cart->remove();
            $order->setIsPaid(1);
            $this->entityManager->flush();
        }


        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
