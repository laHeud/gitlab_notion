<?php

namespace App\Controller;

use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\RichTextPropertyObject;
use Notion\Common\RichText;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\NumberFilter;
use Notion\Databases\Query\Sort;

class NotionController extends AbstractController
{
    protected string $token;

    public function __construct( string $token){
        $this->token = $token;
    }

    #[Route('/notion', name: 'app_notion')]
    public function index(): JsonResponse
    {

    $notion = Notion::create($this->token);

    $databaseId = "410ad313-1241-4643-93e3-4d16ccb743b6";
    $database = $notion->databases()->find($databaseId);

    $query = Query::create()
        ->changeFilter(
                NumberFilter::property("PlusID")->equals(1),
        )
        ->addSort(Sort::property("Due")->ascending())
        ->changePageSize(20);
    
    $result = $notion->databases()->query($database, $query);

    
    
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
