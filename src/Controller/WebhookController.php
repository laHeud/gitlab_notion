<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Gitlab\Client as GitlabClient;
use Brd6\NotionSdkPhp\Client as NotionClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class WebhookController extends AbstractController
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/webhook/gitlab', name: 'app_webhook')]
    public function handleWebhook(Request $request): Response
    {
        $this->logger->debug('Webhook received', [
            'request' => $request->getContent(),
        ]);
        
        $data = json_decode($request->getContent(), true);
        
        // Afficher les données du webhook pour le débogage
        dump($data);die;
   
        
        return new Response('OK');
    }
}

   

