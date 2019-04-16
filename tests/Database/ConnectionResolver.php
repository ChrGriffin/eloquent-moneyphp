<?php

namespace EloquentMoneyPHP\Tests\Database;

use PDO;
use Illuminate\Database\{ConnectionResolverInterface, SQLiteConnection};

class ConnectionResolver implements ConnectionResolverInterface
{

    /**
     * Get a database connection instance.
     *
     * @param  string $name
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function connection($name = null)
    {
        return new SQLiteConnection(new PDO('sqlite::memory:'));
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return 'sqlite';
    }

    /**
     * Set the default connection name.
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultConnection($name)
    {
        return;
    }
}
