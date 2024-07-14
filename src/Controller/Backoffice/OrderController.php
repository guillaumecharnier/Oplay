<?php

namespace App\Controller\Backoffice;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_main', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('backoffice/order/main.html.twig', [
            'orders' => $orderRepository->findAll(),

        ]);
    }

    #[Route('/pending', name: 'app_order_pending', methods: ['GET'])]
    public function pending(OrderRepository $orderRepository): Response
    {
        return $this->render('backoffice/order/pendingOrders.html.twig', [
            'orders' => $orderRepository->findAll(),

        ]);
    }

    #[Route('/validate', name: 'app_order_validate', methods: ['GET'])]
    public function validate(OrderRepository $orderRepository): Response
    {
        return $this->render('backoffice/order/validateOrders.html.twig', [
            'orders' => $orderRepository->findAll(),

        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('backoffice/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_main', [], Response::HTTP_SEE_OTHER);
    }

}

