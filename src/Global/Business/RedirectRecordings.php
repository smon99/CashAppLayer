<?php declare(strict_types=1);

namespace App\Global\Business;

class RedirectRecordings
{
    public array $recordedUrl = [];

    public function sendUrl(string $url): void
    {
        $this->recordedUrl[] = $url;
    }
}