<?php

namespace App\Infrastructure\Persistent\Adapters;

interface MysqlTableInterface
{
    public static function tableDescription(): string;
}
