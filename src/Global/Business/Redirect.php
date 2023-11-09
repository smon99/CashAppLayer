<?php declare(strict_types=1);

namespace App\Global\Business;

class Redirect
{
    public RedirectRecordings $redirectRecordings;
    public function __construct(RedirectRecordings $redirectRecordings)
    {
        $this->redirectRecordings = $redirectRecordings;
    }

    public function redirectTo(string $url): void
    {
        $this->redirectRecordings->sendUrl($url);
        header('Location: ' . $url);
    }
}