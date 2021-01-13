<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchType;
use App\Model\Search;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/our_products", name="products")
     */
    public function index(Request $request): Response
    {
        $products =$this->entityManager->getRepository(Product::class)->findAll();

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }

        return $this->render('product/index.html.twig',
        [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product")
     */
    public function show($slug): Response
    {
        $product =$this->entityManager->getRepository(Product::class)->findOneBy(['slug' => $slug]);

        if (!$product){
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig',
            [
                'product' => $product
            ]);
    }
}
