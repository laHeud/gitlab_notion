<?php

namespace App\Service;



class GitlabService
{

    public function getIdInBranch(array $mr) :string
    {
        preg_match('/feature\/UC-(\d+)-/', $mr['object_attributes']['source_branch'], $matches);

        return $matches[1];
    }

    public function getUrlInBranch(array $mr) :string
    {
        return $mr['object_attributes']['url'];

    }


}