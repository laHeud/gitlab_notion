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
        $file = File::createExternal($propertyValue);
        dd($file);
        $property = \Notion\Pages\Properties\Files::create($file);
        $updatedPage = $page->addProperty($propertyName, $property);

        return $this->client->pages()->update($updatedPage);
    }


    public function appendBlock($pageId, $block)
    {
        return $this->client->blocks()->append($pageId,$block);
    }

    public function filterPropertiesByPrefix($databaseId, Page $page, $value)
    {
        $data = $this->getDatabaseById($databaseId);
        $properties = $data->properties()->getAll();

        $prefix = "Gitlab_";
        $proprietesFiltrees = array_filter(array_keys($properties), function ($propriete) use ($prefix) {
            return substr($propriete, 0, strlen($prefix)) === $prefix;
    });

    foreach($proprietesFiltrees as $prop => $pro1){
        $page->getProperty($pro1)->toArray();

        if( null !== $page->getProperty($pro1)->toArray()['url']){

            if( 0 !== strcmp($page->getProperty($pro1)->toArray()['url'], $value))
            {
            $property = \Notion\Pages\Properties\Files::create($value);
            $updatedPage = $page->addProperty($pro1, $property);
            }
        }
    }

    return $this->client->pages()->update($updatedPage1);
}


}
    