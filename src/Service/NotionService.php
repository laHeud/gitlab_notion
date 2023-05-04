<?php

namespace App\Service;

use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\NumberFilter;
use Notion\Databases\Query\Sort;
use Notion\Databases\Database;
use Notion\Databases\Properties;
use Notion\Pages\Page;

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
        $property = \Notion\Pages\Properties\Url::create($propertyName, $propertyValue);
        $updatedPage = $page->addProperty($propertyName, $property);

        return $this->client->pages()->update($updatedPage);

}

}
