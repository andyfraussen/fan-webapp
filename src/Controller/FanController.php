<?php

namespace App\Controller;

use App\Entity\Fan;
use App\Entity\Product;
use App\Form\FanVerificationType;
use App\Repository\FanRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class FanController extends AbstractController
{
    #[Route('/', name: 'app_fan')]
    public function index(): Response
    {
        $fanVerificationForm = $this->createForm(FanVerificationType::class);

        return $this->render('fan/index.html.twig', [
            'fanVerificationForm' => $fanVerificationForm->createView(),
        ]);
    }

    #[Route('/fan/verify', name: 'app_fan_verify', methods: ['POST'])]
    public function verify(Request $request, FanRepository $fanRepository): JsonResponse
    {
        $fanVerificationForm = $this->createForm(FanVerificationType::class);
        $fanVerificationForm->handleRequest($request);

        if (!$fanVerificationForm->isSubmitted()) {
            return new JsonResponse(['success' => false, 'message' => 'Form not submitted'], 400);
        }

        if (!$fanVerificationForm->isValid()) {
            return new JsonResponse(['success' => false, 'message' => 'Form data is invalid'], 422);
        }

        $data = $fanVerificationForm->getData();
        $fan = $fanRepository->findOneBy([
            'memberNumber' => $data->getMemberNumber(),
            'birthday' => $data->getBirthday()
        ]);

        if (!$fan) {
            return new JsonResponse(['success' => false, 'message' => 'Fan not found'], 200);
        }

        return new JsonResponse(['success' => true, 'message' => 'Fan verified', 'redirectUrl' =>$this->generateUrl('app_fan_orders', ['id' => $fan->getId()])]);
    }

    #[Route('/fan/{id}/orders', name: 'app_fan_orders')]
    public function fanOrders(Fan $fan, ProductRepository $productRepository): Response
    {
        $orders = $fan->getOrders();
        $products = count($orders) === 0 ? $products = $productRepository->findAll() : null;

        return $this->render('fan/orders.html.twig', [
            'orders' => $orders,
            'products' => $products,
            'fan' => $fan
        ]);
    }

    private function createErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['success' => false, 'message' => $message], $statusCode);
    }
}
