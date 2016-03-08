<?php

namespace Vundi\NaEmoji;

use Vundi\Potato\Database;
use PDO;

class FindWhere
{
    /**
     * Return an array after running a query containing a where clause
     */
    public function findResults($query)
    {
        $db = new Database();
        $statement = $db::$db_handler->query($query);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement->fetch();
    }
}
