<?php

namespace App\Helpers;

use App\Models\User;

class Functions
{
    public static function generateUID(string $prefix, string $Model, array $args = ['id' => 3, 'rand' => ['digits' => 3, 'min' => 1, 'max' => 999]]): string
    {
        return $prefix
            . str_pad(($Model::max('id') ?? 0) + 1, $args['id'], '0', STR_PAD_LEFT)
            . str_pad(rand($args['rand']['min'], $args['rand']['max']), $args['rand']['digits'], '0', STR_PAD_LEFT);
    }

}
