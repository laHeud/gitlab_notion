<?php

namespace App\Service;

use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\NumberFilter;
use Notion\Databases\Query\Sort;

class NotionService
{

    private string $token;

    public function __construct(string $token){
        
        $this->token = $token;
    }

    public function token(){

        return Notion::create($this->token);

    }

    public function queryProperty(string $propertyName, $equals){

        $query = Query::create()
        ->changeFilter(
                NumberFilter::property($propertyName)->equals($equals),
        )
        ->addSort(Sort::property("Due")->ascending())
        ->changePageSize(20);

        return $query;
    }

}
