<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use App\Service\NotionService;
use App\Service\GitlabService;
use App\Service\MergeRequestService;


class WebhookController extends AbstractController
{
    private const DATABASE_ID = "410ad313-1241-4643-93e3-4d16ccb743b6";
    private $logger;
    private $notion;
    private $gitlab;
    private $mr;


    public function __construct(
        LoggerInterface $logger, 
        NotionService $notion, 
        GitlabService $gitlab,
        MergeRequestService $mr
        )
    {
        $this->logger = $logger;
        $this->notion = $notion;
        $this->gitlab = $gitlab;
        $this->mr = $mr;
    }

    #[Route('/webhook/gitlab', name: 'app_webhook')]
    public function handleWebhook(Request $request): Response
    {

        $this->logger->debug('Webhook received', [$request->getContent()]);


        $data = json_decode($request->getContent());

        // if ($data === null) {
        //     throw new \Exception('Bad JSON body from Stripe!');
        // }
        

        $id = $this->gitlab->getIdInBranch($data->object_attributes->source_branch);
        $url = $data->object_attributes->url;
        $description = $data->object_attributes->description;


        $database = $this->notion->getDatabaseById(self::DATABASE_ID);
        $result = $this->notion->queryPagesByPropertyId($database,"PlusID", $id);
        $page = $this->notion->getPageById($result[0]->id);

        $urlNotion = $page->url;

        if (strpos($description, $urlNotion) === false) {

            $newDescription = "Pour plus d'informations, consultez [$id]($urlNotion)\n\n" . $description;

            $this->mr->setBranchDescription($data->object_attributes->iid, ["description" => $newDescription]);
        }


        /** @var \Notion\Pages\Properties\RichTextProperty $property */
        $property = $page->getProperty("Gitlab");
       
        // Send to Notion
        $this->notion->updatePagePropertyLink($page, "Gitlab",$url );


        return new Response('OK');
    }
}
