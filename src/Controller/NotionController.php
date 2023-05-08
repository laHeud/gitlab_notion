<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotionService;
use Notion\Blocks\Paragraph;
use Notion\Common\RichText;
use Notion\Common\Color;
//use NotionClient;

class NotionController extends AbstractController
{
    public NotionService $notion;
 //   public NotionClient $notionClient;
    private const DATABASE_ID = "410ad313-1241-4643-93e3-4d16ccb743b6";

    public function __construct( NotionService $notion, 
    //NotionClient $notionClient
    ){
        
        $this->notion = $notion;
        //$this-> notionClient = $notionClient;
    }

    #[Route('/notion', name: 'app_notion')]

    public function index(): Response
    {

    $database = $this->notion->getDatabaseById(self::DATABASE_ID);
    
    $result = $this->notion->queryPagesByPropertyId($database,"PlusID", 8);
    
    $page = $this->notion->getPageById($result[0]->id);
    //dd($database->properties()->getAll());
    //$this->notion->filterPropertiesByPrefix((self::DATABASE_ID),$page, 'lala');
    $this->notion->updatePagePropertyLink($page, "Gitlab", "https://url23.com");
    //$this->notion->updatePagePropertyLink($page, "Gitlab", "https://gitlab.com/unagi-games/test-webhook/-/merge_requests/2");



    // $lol = $this->block->fromArray($requestBody);
    // dd($lol);

// Effectuez une requête à l'API Notion en utilisant l'endpoint approprié.
//$lala = Paragraph::create()->changeText([RichText::fromString('👨‍💻lala jai reussi')->color(Color::Orange)->bold()]);;
//$this->notion->appendBlock($result[0]->id, [$lala]); 

    
    // Build the data array to pass to the view
    $data = [
        'updates' => [
            [
                'field' => 'Gitlab',
                'value' => 'https://gitlab.com/unagi-games/test-webhook/-/merge_requests/2'
            ]
        ]
    ];

     /** @var \Notion\Pages\Properties\RichTextProperty $property */
     //$property = $page->getProperty("Gitlab");
     //dd($property->toArray()['url']);

    // // Update property
    // $updatedRelease = \Notion\Pages\Properties\RichTextProperty::fromString("bonjour");
    // $uppage = $page->addProperty("name", $updatedRelease);

    // // Send to Notion
    // $notion->pages()->update($uppage);

    // Render the view using the Twig template
    return $this->render('update.html.twig', $data);

    }
}
