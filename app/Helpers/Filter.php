<?php

namespace App\Helpers;

class Filter
{
    public static function prepare(array $data)
    {
        $trash              = $data['filter']['trash']      ?? $data['default']['filter']['trash']      ?? false;
        $order_column       = $data['order']['column']      ?? $data['default']['order']['column']      ?? 'created_at';
        $order_direction    = $data['order']['direction']   ?? $data['default']['order']['direction']   ?? 'desc';
        $per_page           = $data['pagination']['limit']  ?? $data['default']['pagination']['limit']  ?? 10;


        return [
            'trash' => $trash,
            'order_column' => $order_column,
            'order_direction' => $order_direction,
            'per_page' => $per_page,
        ];
    }


    public static function search($query, array $searches)
    {
        if(empty($searches)) return $query;

        $searches = self::search_values($searches);

        foreach ($searches as $index => $search) {
            if (is_array($search)) {
                if(isset($search['column'], $search['value'])){

                    $column     = $search['column'];
                    $value      = $search['value'];
                    $operator   = $search['operator']   ?? '=';
                    $boolean    = $search['boolean']    ?? 'AND';

                    if($index > 0 && $boolean == 'OR'){
                        $query->orWhere($column, $operator, $value);
                    } else {
                        $query->where($column, $operator, $value);
                    }


                } else {
                    $query->where(function ($query) use ($search) {
                        return self::search($query, $search);
                    });
                }
            }
        }

        return $query;
    }

    public static function search_values($array) {
        $result = [];
        foreach ($array as $key => $value) {
            if(is_array($value)){
                $result[] = self::search_values($value);
            } else if(!empty($value)){
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
