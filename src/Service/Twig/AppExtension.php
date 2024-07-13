<?php

namespace App\Service\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('mask_key', [$this, 'maskKey']),
        ];
    }

    public function maskKey(string $key, int $visibleChars = 5): string
    {
        $masked = str_repeat('*', strlen($key) - $visibleChars);
        return substr($key, 0, $visibleChars) . $masked;
    }
}