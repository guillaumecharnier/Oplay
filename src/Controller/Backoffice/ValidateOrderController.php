<?php

namespace App\Controller\Backoffice;

use App\Entity\ValidateOrder;
use App\Form\ValidateOrderType;
use App\Repository\ValidateOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back/validate/order')]
class ValidateOrderController extends AbstractController
{
    #[Route('/', name: 'app_validate_order_index')]
    public function index(ValidateOrderRepository $validateOrderRepository): Response
    {
        $validateOrders = $validateOrderRepository->findAllWithUser();

        return $this->render('backoffice/validate_order/index.html.twig', [
            'validateOrders' => $validateOrders,
        ]);
    }

    #[Route('/{id}', name: 'app_validate_order_show', methods: ['GET'])]
    public function show(ValidateOrder $validateOrder): Response
    {
        return $this->render('backoffice/validate_order/show.html.twig', [
            'validate_order' => $validateOrder,
        ]);
    }

    #[Route('/{id}', name: 'app_validate_order_delete', methods: ['POST'])]
    public function delete(Request $request, ValidateOrder $validateOrder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$validateOrder->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($validateOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_validate_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
