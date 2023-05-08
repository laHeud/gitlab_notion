<?php

namespace App\Service;

use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\NumberFilter;
use Notion\Databases\Query\Sort;
use Notion\Databases\Database;
use Notion\Databases\Properties;
use Notion\Pages\Page;
use Notion\Common\File;
use Notion\Pages\Properties\Files;

class NotionService
{

    private string $token;
    private Notion $client;

    public function __construct(string $token){
        $this->client = Notion::create($token);
    }


    public function getDatabaseById(string $databaseId): ?Database
    {
        return $this->client->databases()->find($databaseId);
    }
    
    public function queryPagesByPropertyId(Database $database, string $propertyName, $equals): array
    {
        $query = Query::create()
        ->changeFilter(
                NumberFilter::property($propertyName)->equals($equals),
        )
        ->addSort(Sort::property("Due")->ascending())
        ->changePageSize(20);

        $result = $this->client->databases()->query($database, $query);

        return $result->pages ?? [];
    }
    
    public function getPageById(string $pageId): ?Page
    {
        return $this->client->pages()->find($pageId);
    }
    
    public function updatePagePropertyLink(Page $page, string $propertyName, string $propertyValue): Page
    {
        // Récupérer la propriété existante.
    $currentProperty = $page->getProperty($propertyName);
        // Créer le nouveau fichier que vous voulez ajouter à la propriété
    $newFile = File::createExternal($propertyValue)->changeName('Gitlab');

    if (is_null($currentProperty)) {
        $currentProperty = Files::create();
    }

// Ajouter le nouveau fichier à la propriété existante, en utilisant la méthode addFile.
    $updatedProperty = $currentProperty->addFile($newFile);

// Mettre à jour la propriété pour la page donnée.
    $updatedPage = $page->addProperty('Gitlab', $updatedProperty);

// Enregistrer la modification sur Notion
    return $this->client->pages()->update($updatedPage);
    }


    public function appendBlock($pageId, $block)
    {
        return $this->client->blocks()->append($pageId,$block);
    }

}
    