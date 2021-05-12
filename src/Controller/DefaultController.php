<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;

use App\Service\Calculator;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/calculate', name: 'calculate', methods: ['POST'])]
    public function calculate(Request $request, Calculator $calculator): JsonResponse|RedirectResponse
    {
        $stringToCalculate = json_decode($request->request->get('json', '"0"'));

        $response = new JsonResponse();
        $response->setData($calculator->calculate($stringToCalculate));
        return $response;
    }
}
