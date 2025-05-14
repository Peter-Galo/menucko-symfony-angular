<?php

namespace App\Service;

use App\Entity\Recept;
use App\Repository\ReceptRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use TCPDF;

class MenuService
{
    private ReceptRepository $receptRepository;
    private CacheInterface $cache;
    private const CACHE_KEY = 'weekly_menu';

    public function __construct(ReceptRepository $receptRepository, CacheInterface $cache)
    {
        $this->receptRepository = $receptRepository;
        $this->cache = $cache;
    }

    public function getWeeklyMenu(): array
    {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 hour or adjust as needed
            return $this->buildMenu();
        });
    }

    public function regenerateMenu(): array
    {
        $this->cache->delete(self::CACHE_KEY);
        return $this->getWeeklyMenu();
    }

    private function buildMenu(): array
    {
        $all = $this->receptRepository->findAll();
        $grouped = ['masko' => [], 'veg' => []];

        foreach ($all as $r) {
            $grouped[$r->getCategory() === 'veg' ? 'veg' : 'masko'][] = $r;
        }

        $weekendMeal = $this->pickRandomMeal(
            array_filter(array_merge($grouped['masko'], $grouped['veg']), fn($r) => $r->getDays() === 2)
        );

        if ($weekendMeal) {
            $grouped['masko'] = array_filter($grouped['masko'], fn($r) => $r !== $weekendMeal);
            $grouped['veg'] = array_filter($grouped['veg'], fn($r) => $r !== $weekendMeal);
        }

        return [
            'weekdays' => $this->buildWeekdayMenu($grouped['masko'], $grouped['veg']),
            'weekend' => $this->buildWeekendMenu($weekendMeal),
        ];
    }

    private function buildWeekdayMenu(array $masko, array $veg): array
    {
        $menu = [];
        $dayNames = ['Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok'];
        $current = $masko;
        $i = 0;
        $remaining = 5;

        while ($remaining > 0 && $i < count($dayNames)) {
            if (empty($current)) {
                $current = $current === $masko ? $veg : $masko;
                continue;
            }

            $meal = $this->pickRandomMeal($current);

            if (!$meal || $meal->getDays() > $remaining) {
                continue;
            }

            for ($j = 0; $j < $meal->getDays() && $remaining > 0; $j++) {
                $menu[] = ['day' => $dayNames[$i], 'meal' => $meal];
                $remaining--;
                $i++;
            }

            $current = $current === $masko ? $veg : $masko;
        }

        return $menu;
    }

    private function buildWeekendMenu(?Recept $meal): array
    {
        return [
            ['day' => 'Sobota', 'meal' => $meal],
            ['day' => 'Nedeľa', 'meal' => $meal],
        ];
    }

    private function pickRandomMeal(array $meals): ?Recept
    {
        return empty($meals) ? null : $meals[array_rand($meals)];
    }

    public function generateWeeklyMenuPdf(): StreamedResponse
    {
        $menu = $this->getWeeklyMenu();

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, 'Týždenné meňu', 0, 1, 'C');
        $pdf->Ln(5);

        $this->addSectionToPdf($pdf, 'Týždeň', $menu['weekdays']);
        $this->addSectionToPdf($pdf, 'Víkend', $menu['weekend']);

        return new StreamedResponse(function () use ($pdf) {
            $pdf->Output('tyzdenne_menu.pdf', 'D');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="tyzdenne_menu.pdf"',
        ]);
    }

    private function addSectionToPdf(TCPDF $pdf, string $sectionTitle, array $items): void
    {
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, $sectionTitle, 0, 1);
        $pdf->Ln(2);
        $pdf->SetFont('dejavusans', '', 11);

        foreach ($items as $item) {
            /** @var Recept $meal */
            $meal = $item['meal'];
            $details = sprintf(
                "%s (%s)%s - %d %s",
                $meal->getTitle(),
                $meal->getCategory(),
                $meal->getType() ? " - " . $meal->getType() : '',
                $meal->getDays(),
                $meal->getDays() > 1 ? 'dni' : 'deň'
            );

            $pdf->MultiCell(0, 7, $item['day'] . ": " . $details, 0, 'L');
        }

        $pdf->Ln(4);
    }
}
