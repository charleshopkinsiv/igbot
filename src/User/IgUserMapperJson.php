<?php

namespace IgBot\User;


class IgUserMapperJson
{

    private int $limit;
    private int $count;

    public static string $users_file = __DIR__ . "/../../data/users.json";
    private static string $users_img_dir = __DIR__ . "/../../../../../public/images/users/icons";

    public function __construct()
    {

        $this->limit = $this->count = 0;
    }

    public function insert(IgUser $User)
    {

        $USERS = $this->loadUsersArray();
        $USERS[$User->getUsername()] = $User->toArray();
        $this->saveUsers($USERS);
    }

    public function loadUsersArray() : array
    {

        $USERS = [];

        if(file_exists(self::$users_file))
            $USERS = (array)json_decode(file_get_contents(self::$users_file), 1);

        return $USERS;
    }


    public function getByUsername(string $username)
    {

        foreach($this->loadUsersArray() as $USER)
            if($USER['username'] == $username)
                return new IgUser(
                    $USER['username'],
                    $USER['name'],
                    $USER['description']
                );
    }


    private function saveUsers(array $USERS)
    {

        file_put_contents(self::$users_file, json_encode($USERS));
    }

    public function saveUserImage(string $username, $img_binary)
    {
        $username_img_dir = self::userImageDir($username);
        

        if(!file_exists($username_img_dir)) // Create directory if it doesn't exist
            mkdir($username_img_dir, 0777, true);

        file_put_contents($username_img_dir . "/" . $username . ".jpg", $img_binary);
    }

    public function limit(int $limit)
    {

        $this->limit = $limit;
    }

    public function getCollection() : IgUserCollection
    {

        $Collection = new IgUserCollection();

        $USERS = $this->loadUsersArray();
        $this->count = count($USERS);

        $i = 0;
        foreach($USERS as $USER) {

            $Collection->add(new IgUser(
                $USER['username'],
                $USER['name'],
                $USER['description']
            ));

            $i++;
            if($i >= $this->limit) break;
        }

        return $Collection;
    }


    public function count() : int
    {

        if(empty($this->count))
            $this->count = count($this->loadUsersArray());

        return $this->count;
    }


    public static function userImageDir(string $username)
    {

        return self::$users_img_dir . "/" . substr($username, 0, 1);
    }
}