<?php

namespace App\Controller;

use App\Entity\Fan;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\CreateOrderType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    #[Route('/fan/{id}/orders', name: 'app_fan_orders', methods: ['GET'])]
    public function index(Fan $fan, ProductRepository $productRepository): Response
    {
        $orders = $fan->getOrders();

        $products = count($orders) === 0 ? $products = $productRepository->findAll() : null;
        $orderForm = $this->createForm(CreateOrderType::class);

        return $this->render('fan/orders.html.twig', [
            'orders' => $orders,
            'products' => $products,
            'fan' => $fan,
            'orderForm' => $orderForm
        ]);
    }

    #[Route('/fan/{id}/orders', name: 'app_fan_orders_create', methods: ['POST'])]
    public function createOrder(Fan $fan, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        if (count($fan->getOrders()) !== 0) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An order already exists for this fan.',
                'redirectUrl' => $this->generateUrl('app_fan_orders', ['id' => $fan->getId()])
            ]);
        }

        $orderCreateForm = $this->createForm(CreateOrderType::class);
        $orderCreateForm->handleRequest($request);

        if (!$orderCreateForm->isSubmitted()) {
            return new JsonResponse(['success' => false, 'message' => 'Form not submitted'], 400);
        }

        if (!$orderCreateForm->isValid()) {
            return new JsonResponse(['success' => false, 'message' => 'Form data is invalid'], 422);
        }

        $order = new Order();
        $order->setFan($fan);
        $order->setProduct($entityManager->getRepository(Product::class)->find($request->request->all()['create_order_type']['product']));

        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Order confirmed', 'redirectUrl' => $this->generateUrl('app_fan_orders', ['id' => $fan->getId()])]);

    }
}
