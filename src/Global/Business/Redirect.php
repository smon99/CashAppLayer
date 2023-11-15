<?php declare(strict_types=1);

namespace App\Global\Business;

class Redirect
{
    public function redirectTo(string $url): void
    {
        header('Location: ' . $url);
    }
}