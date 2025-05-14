<?php

namespace App\Controller;

use App\Entity\Recept;
use App\Repository\ReceptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/recepty')]
final class ReceptyController extends AbstractController
{
    #[Route('', name: 'api_recepty_index', methods: ['GET'])]
    public function index(ReceptRepository $receptRepository): JsonResponse
    {
        $recepty = $receptRepository->findAllOrderedByCategory();

        $grouped = [];

        foreach ($recepty as $recept) {
            $category = $recept->getCategory();
            $type = $recept->getType() ?? 'null';

            $grouped[$category][$type][] = [
                'id' => $recept->getId(),
                'uuid' => $recept->getUuid(),
                'title' => $recept->getTitle(),
                'createdAt' => $recept->getCreatedAt()->format('Y-m-d H:i:s'),
                'category' => $category,
                'type' => $type,
                'days' => $recept->getDays(),
            ];
        }

        // Sort keys as before
        ksort($grouped, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($grouped as &$types) {
            ksort($types, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return $this->json($grouped);
    }

    #[Route('', name: 'api_recepty_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || !isset($data['category'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $recept = new Recept();
        $recept->setTitle($data['title']);
        $recept->setCategory($data['category']);
        $recept->setType($data['type'] ?? null);
        $recept->setDays($data['days'] ?? null);

        // Auto-clear type if category is 'masko'
        if ($recept->getCategory() === 'masko') {
            $recept->setType(null);
        }
        $recept->setCreatedAt(new \DateTimeImmutable());

        try {
            $em->persist($recept);
            $em->flush();
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

        return $this->json([
            'success' => true,
            'id' => $recept->getId(),
            'uuid' => $recept->getUuid(),
        ], 201);
    }
}
