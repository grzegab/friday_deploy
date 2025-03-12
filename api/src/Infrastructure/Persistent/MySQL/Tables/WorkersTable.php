<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Tables;

use App\Infrastructure\Persistent\Adapters\MysqlTableInterface;

class WorkersTable implements MysqlTableInterface
{
    public const TABLE = 'workers';
    public const ID = 'id';
    public const UUID = 'uuid';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const EMAIL = 'email';
    public const PHONE = 'phone';
    public const COMPANY_ID = 'company_id';

    public static function tableDescription(): string
    {
        $create = sprintf('CREATE TABLE IF NOT EXISTS %s ', self::TABLE);
        $create .= sprintf('(%s INT AUTO_INCREMENT PRIMARY KEY, ', self::ID);
        $create .= sprintf('%s CHAR(36) NOT NULL UNIQUE, ', self::UUID);
        $create .= sprintf('%s VARCHAR(40) NOT NULL, ', self::FIRST_NAME);
        $create .= sprintf('%s VARCHAR(40) NOT NULL, ', self::LAST_NAME);
        $create .= sprintf('%s VARCHAR(100) NOT NULL, ', self::EMAIL);
        $create .= sprintf('%s VARCHAR(20), ', self::PHONE);
        $create .= sprintf('FOREIGN KEY (%s) REFERENCES companies(id) ON DELETE CASCADE', self::COMPANY_ID);
        $create .= ') ENGINE=InnoDB;';

        return $create;
    }
}
