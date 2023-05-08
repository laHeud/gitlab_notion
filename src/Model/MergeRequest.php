<?php

namespace App\Model;

class MergeRequest
{
    private string $id;
    private string $url;
    private string $title;
    private string $iid;
    private string $description;

    public function __construct(string $id, string $url, string $title, string $iid, string $description)
    {
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->iid = $iid;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIid(): string
    {
        return $this->iid;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
