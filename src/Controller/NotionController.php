<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotionService;

class NotionController extends AbstractController
{
    public NotionService $notion;

    public function __construct( NotionService $notion){
        
        $this->notion = $notion;
    }

    #[Route('/notion', name: 'app_notion')]

    public function index(): JsonResponse
    {

    $notion = $this->notion->token();

    $databaseId = "410ad313-1241-4643-93e3-4d16ccb743b6";
    $database = $notion->databases()->find($databaseId);
    
    $result = $notion->databases()->query($database, $this->notion->queryProperty("PlusID",1));

    $pages = $result->pages; // array of Page
    $result->hasMore; // true or false
    $result->nextCursor; // cursor ID or null

    $page = $notion->pages()->find($pages[0]->id);
    
    /** @var \Notion\Pages\Properties\RichTextProperty $property */
    $property = $page->getProperty("name");

    // Update property
    $updatedRelease = \Notion\Pages\Properties\RichTextProperty::fromString("bonjour");
    $uppage = $page->addProperty("name", $updatedRelease);

    // Send to Notion
    $notion->pages()->update($uppage);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/NotionController.php',
        ]);
    }
}
