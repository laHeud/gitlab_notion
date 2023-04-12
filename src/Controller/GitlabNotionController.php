<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Gitlab\Client;

class GitlabNotionController extends AbstractController
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    #[Route('/gitlab/notion', name: 'app_gitlab_notion')]
    public function index(): JsonResponse
    {
        $issues = $this->client->mergeRequests()->update();
        dump($issues);
        die;
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/GitlabNotionController.php',
        ]);
    }
}
