<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Tables;

use App\Infrastructure\Persistent\Adapters\MysqlTableInterface;

class CompaniesTable implements MysqlTableInterface
{
    public const TABLE = 'companies';
    public const ID = 'id';
    public const UUID = 'uuid';
    public const COMPANY_NAME = 'company_name';
    public const TAX_NUMBER = 'tax_number';
    public const ADDRESS = 'address';
    public const CITY = 'city';
    public const POSTCODE = 'postcode';

    public static function tableDescription(): string
    {
        $create = sprintf('CREATE TABLE IF NOT EXISTS %s ', self::TABLE);
        $create .= sprintf('(%s INT AUTO_INCREMENT PRIMARY KEY, ', self::ID);
        $create .= sprintf('%s CHAR(36) NOT NULL UNIQUE, ', self::UUID);
        $create .= sprintf('%s VARCHAR(100) NOT NULL, ', self::COMPANY_NAME);
        $create .= sprintf('%s VARCHAR(15) NOT NULL, ', self::TAX_NUMBER);
        $create .= sprintf('%s VARCHAR(50) NOT NULL, ', self::ADDRESS);
        $create .= sprintf('%s VARCHAR(50) NOT NULL, ', self::CITY);
        $create .= sprintf('%s VARCHAR(10) NOT NULL, ', self::POSTCODE);
        $create .= ') ENGINE=InnoDB;';

        return $create;
    }
}
