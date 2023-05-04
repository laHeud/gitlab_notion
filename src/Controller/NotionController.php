<?php

namespace App\Controller;

use App\Service\NotionService;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\RichTextPropertyObject;
use Notion\Common\RichText;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\NumberFilter;
use Notion\Databases\Query\Sort;
use Symfony\Component\HttpFoundation\Response;

class NotionController extends AbstractController
{
    protected NotionService $notion;

    public function __construct(NotionService $notion){
        $this->notion = $notion;
    }

    #[Route('/notion', name: 'app_notion')]
    public function index(): Response
    {

    $databaseId = "410ad313-1241-4643-93e3-4d16ccb743b6";
    $database = $this->notion->getDatabaseById($databaseId);
    
    $result = $this->notion->queryPagesByPropertyId($database,"PlusID", 1234);

    $page = $this->notion->getPageById($result[0]->id);

    $this->notion->updatePagePropertyLink($page, "Gitlab", "https://gitlab.com/unagi-games/test-webhook/-/merge_requests/2");

// Build the data array to pass to the view
$data = [
    'updates' => [
        [
            'field' => 'Gitlab',
            'value' => 'https://gitlab.com/unagi-games/test-webhook/-/merge_requests/2'
        ]
    ]
];

// Render the view using the Twig template
return $this->render('update.html.twig', $data);
    }    
}
