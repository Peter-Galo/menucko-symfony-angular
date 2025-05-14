<?php

namespace App\Controller;

use App\Service\MenuService;
use App\Entity\Recept;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/menu')]
class MenuController extends AbstractController
{
    #[Route('', name: 'api_menu_index', methods: ['GET'])]
    public function index(MenuService $menuService): JsonResponse
    {
        return $this->json($this->normalizeMenu($menuService->getWeeklyMenu()));
    }

    #[Route('/generate', name: 'api_menu_generate', methods: ['GET'])]
    public function generate(MenuService $menuService): JsonResponse
    {
        return $this->json($this->normalizeMenu($menuService->regenerateMenu()));
    }

    #[Route('/pdf', name: 'api_menu_pdf', methods: ['GET'])]
    public function downloadPdf(MenuService $menuService): StreamedResponse
    {
        return $menuService->generateWeeklyMenuPdf();
    }

    private function normalizeMenu(array $raw): array
    {
        return [
            'weekdays' => array_map([$this, 'normalizeDayMeal'], $raw['weekdays']),
            'weekend' => array_map([$this, 'normalizeDayMeal'], $raw['weekend']),
        ];
    }

    private function normalizeDayMeal(array $item): array
    {
        $meal = $item['meal'];

        return [
            'day' => $item['day'],
            'meal' => $meal ? [
                'id' => $meal->getId(),
                'uuid' => $meal->getUuid(),
                'title' => $meal->getTitle(),
                'category' => $meal->getCategory(),
                'type' => $meal->getType(),
                'days' => $meal->getDays(),
            ] : null,
        ];
    }
}
