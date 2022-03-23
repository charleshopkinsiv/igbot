<?php

namespace igbot\user;


class IgUser {

    private string $username, $name, $description;

    public function __construct($username, $name = "", $description = "", $scraper = 0) {

        $this->username         = $username;
        $this->name             = $name;
        $this->description      = $description;
        $this->scraper          = $scraper;
    }

    public function getScraper() { return $this->scraper; }

    public function getUsername() { return $this->username; }
    public function setUsername(string $username) { $this->username = $username; }

    public function getName() { return $this->name; }
    public function setName(string $name) { $this->name = $name; }

    public function getDescription() { return $this->description; }
    public function setDescription(string $description) { $this->description = $description; }

    public function getImageUrl() 
    {

        return "/public/images/users/icons/" . substr($this->getUsername(), 0, 1) . "/" . $this->getUsername() . ".jpg";
    }

    public function toArray() : array
    {

        return [
            'username'      => $this->username,
            'name'          => $this->name,
            'description'   => $this->description,
        ];
    }
}