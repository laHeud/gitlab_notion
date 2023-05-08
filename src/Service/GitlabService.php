<?php

namespace App\Service;

use Gitlab\Client;

class GitlabService
{
    public const ID_PROJET = 45064781;

    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIdInBranch(string $source): string
    {
        preg_match('/feature\/UC-(\d+)-/', $source, $matches);

        return $matches[1];
    }
}
