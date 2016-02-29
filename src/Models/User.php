<?php

namespace Vundi\NaEmoji\Models;

use Vundi\Potato\Model;

class User extends Model
{
    //table to use in the database
    protected static $entity_table = 'User';

    public static function getPerson($query)
    {
        $results = Connection::db()->query($query);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        var_dump($results->fetch());
    }
}
