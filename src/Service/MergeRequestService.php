<?php

namespace App\Service;

class MergeRequestService
{
    protected GitlabService $gitlabService;

    public function __construct(GitlabService $gitlabService)
    {
        $this->gitlabService = $gitlabService;
    }

    public function setBranchDescription(int $iid, array $parameters)
    {
        $this->gitlabService->getClient()->mergeRequests()->update(GitlabService::ID_PROJET, $iid, $parameters);
    }
}