<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudURLGenerator;

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator  $crudUrlGenerator){
        $this->entityManager = $entityManager;
        $this->crudURLGenerator= $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Work in progress', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Shipping in progress', 'fas fa-truck')->linkToCrudAction('updateDelivery');
        return $actions
            ->add('detail',$updatePreparation)
            ->add('detail',$updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreparation(AdminContext $context){

        $order = $context->getEntity()->getInstance();
        $order->setState(2);

        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'> <strong>The order". $order->getReference()." is on progress.</strong>");
        $url = $this->crudURLGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context){

        $order = $context->getEntity()->getInstance();
        $order->setState(3);

        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:blue;'> <strong>The order". $order->getReference()." is coming.</strong>");

        $url = $this->crudURLGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt'),
            TextField::new('user.fullname', 'User'),
            TextEditorField::new('delivery', 'Delivery Address')->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'Delivery price')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'unpaid' => 0,
                'paid' => 1,
                'in progress' => 2,
                'delivery incoming' => 3
            ]),
            ArrayField::new('orderDetails', 'Product purchase')->hideOnIndex()
        ];
    }

}
