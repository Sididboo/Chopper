<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account/order", name="account_order")
     */
    public function index(): Response
    {
        $severalOrder = $this->entityManager->getRepository(Order::class)->findSuccessOrder($this->getUser());
        return $this->render('account/order.html.twig', [
            'severalOrder' => $severalOrder
        ]);
    }


    /**
     * @Route("/account/order/{reference}", name="account_order_show")
     */
    public function show($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
        }
//        dd($severalOrder);
        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
