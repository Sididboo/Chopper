<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use App\Model\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findBy(['isBest' => 1]);
        $header = $this->entityManager->getRepository(Header::class)->findAll();
        return $this->render('home/index.html.twig',
        [
            'products' => $products,
            'headers' => $header
        ]);
    }
}
