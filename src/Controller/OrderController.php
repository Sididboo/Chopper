<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Model\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/order", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {

        if (!$this->getUser()->getSeveralAddress()->getValues()){
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null,[
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/order/resume", name="order_resume", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {


        $form = $this->createForm(OrderType::class, null,[
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $order = new Order;
            $date = new \DateTime();

            $carrier = $form->get('carrier')->getData();
            $delivery = $form->get('addresses')->getData();

            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastName();
            $delivery_content .= '<br>'. $delivery->getPhone();

            if ($delivery->getCompany()) {
                $delivery_content .=' <br>'.$delivery->getCompany();
            }

            $delivery_content .= '<br>'. $delivery->getAddress();
            $delivery_content .= '<br>'. $delivery->getPostal().' '. $delivery->getCity();
            $delivery_content .= '<br>'. $delivery->getCountry();

            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);

            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());

            $order->setDelivery($delivery_content);
            $order->setState(0);

            $this->entityManager->persist($order);

            foreach ($cart->getFull() as $product)
            {

                $order_detail = new OrderDetails();
                $order_detail->setMyOrder($order);
                $order_detail->setProduct($product['product']->getName());
                $order_detail->setQuantity($product['quantity']);
                $order_detail->setPrice($product['product']->getPrice());
                $order_detail->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($order_detail);
                
            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carrier,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()
            ]);
        }

        return $this->redirectToRoute('cart');

    }
}
