<?php


namespace Zfegg\ApiRestfulHandler\Utils;


use Laminas\Db\Sql\Select;

class QueryParser
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = self::normalize($config);
    }

    private static function normalize(array $config): array
    {
        $filters = [];
        foreach ($config as $key => $item) {
            if (is_int($key)) {
                $filters[$item] = ['eq'];
            } else {
                $filters[$key] = (array) $item;
            }
        }

        return $filters;
    }

    public function parseSelect(array $params, Select $select)
    {
        $operatorMap = [
            'eq'         => 'equalTo',
            'neq'        => 'notEqualTo',
            'lt'         => 'lessThan',
            'lte'        => 'lessThanOrEqualTo',
            'gt'         => 'greaterThan',
            'gte'        => 'greaterThanOrEqualTo',
            'startswith' => 'like',
            'endswith'   => 'like',
            'contains'   => 'like',
            'isnull'     => 'isNull',
        ];

        $arrowFields = array_keys($this->config);
        foreach ($params as $field => $items) {
            if (! in_array($field, $arrowFields)) {
                continue;
            }

            if (is_string($items)) {
                $defaultOperator = current($this->config[$field]);
                $items = [$defaultOperator => $items];
            }

            foreach ($items as $operator => $value) {
                if (! isset($operatorMap[$operator])) {
                    continue;
                }

                switch ($operator) {
                    case 'startswith':
                        $value .= '%';
                        break;
                    case 'endswith':
                        $value = '%' . $value;
                        break;
                    case 'contains':
                        $value = '%' . $value . '%';
                        break;
                    default:
                        break;
                }
                $select->where->{$operatorMap[$operator]}($field, $value);
            }
        }
    }
}