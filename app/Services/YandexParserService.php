<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;

class YandexParserService
{
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Сохраняет компанию в БД.
     *
     * На данный момент парсинг отзывов не реализован из-за необходимости
     * использования headless-браузера (Puppeteer) для динамической подгрузки.
     * Вместо этого используется заглушка с тестовыми данными для демонстрации.
     *
     * @TODO: Реализовать сбор отзывов через Puppeteer.
     */
    public function saveToDatabase(string $url): Company
    {
        // Извлекаем ID организации
        $yandexId = $this->extractId($url);
        if (!$yandexId) {
            $yandexId = 'unknown';
        }

        // ВРЕМЕННАЯ ЗАГЛУШКА — для демонстрации работы интерфейса
        // В реальном проекте здесь должен быть парсинг HTML или вызов микросервиса
        $name = 'Тестовая компания (парсинг в разработке)';
        $rating = 4.5;
        $reviewCount = 123;

        // Сохраняем компанию
        $company = Company::updateOrCreate(
            ['url' => $url],
            [
                'yandex_id' => $yandexId,
                'name' => $name,
                'rating' => $rating,
                'rating_count' => $reviewCount,
                'review_count' => $reviewCount,
                'last_parsed_at' => now(),
            ]
        );

        // Очищаем старые отзывы (пока нет парсинга)
        $company->reviews()->delete();

        return $company;
    }

    /**
     * Извлекает ID организации из ссылки.
     */
    private function extractId(string $url): ?string
    {
        try {
            $response = Http::withOptions(['allow_redirects' => ['track_redirects' => true]])
                ->timeout(10)
                ->get($url);
            $finalUrl = $response->effectiveUri()?->__toString();
            if ($finalUrl) {
                $url = $finalUrl;
            }
        } catch (\Exception $e) {}

        if (preg_match('#/org/[^/]+/(\d+)#', $url, $m)) return $m[1];
        if (preg_match('#/chain/[^/]+/(\d+)#', $url, $m)) return $m[1];
        if (preg_match('#[?&]oid=(\d+)#', $url, $m)) return $m[1];
        return null;
    }
}