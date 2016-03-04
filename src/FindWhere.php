<?php

namespace Vundi\NaEmoji;

use Vundi\Potato\Database;
use PDO;

class FindWhere
{
    public function findResults($query)
    {
        $db = new Database();
        $statement = $db::$db_handler->query($query);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement->fetch();
    }
}
