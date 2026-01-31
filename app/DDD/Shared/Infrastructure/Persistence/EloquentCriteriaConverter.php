<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Persistence;

use App\DDD\Shared\Domain\Criteria\Criteria;
use App\DDD\Shared\Domain\Criteria\Filter;
use App\DDD\Shared\Domain\Criteria\FilterOperator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Convierte Criteria de dominio a query builder de Eloquent
 */
final class EloquentCriteriaConverter
{
    public static function apply(Builder $query, Criteria $criteria): Builder
    {
        // Aplicar filtros
        foreach ($criteria->filters() as $filter) {
            $query = self::applyFilter($query, $filter);
        }

        // Aplicar orden
        if ($criteria->order() !== null) {
            $query->orderBy(
                $criteria->order()->field,
                $criteria->order()->direction->value
            );
        }

        // Aplicar limit
        if ($criteria->limit() !== null) {
            $query->limit($criteria->limit());
        }

        // Aplicar offset
        if ($criteria->offset() !== null) {
            $query->offset($criteria->offset());
        }

        return $query;
    }

    private static function applyFilter(Builder $query, Filter $filter): Builder
    {
        return match ($filter->operator) {
            FilterOperator::EQUAL => $query->where($filter->field, '=', $filter->value),
            FilterOperator::NOT_EQUAL => $query->where($filter->field, '!=', $filter->value),
            FilterOperator::GREATER_THAN => $query->where($filter->field, '>', $filter->value),
            FilterOperator::GREATER_THAN_OR_EQUAL => $query->where($filter->field, '>=', $filter->value),
            FilterOperator::LESS_THAN => $query->where($filter->field, '<', $filter->value),
            FilterOperator::LESS_THAN_OR_EQUAL => $query->where($filter->field, '<=', $filter->value),
            FilterOperator::LIKE => $query->where($filter->field, 'LIKE', '%' . $filter->value . '%'),
            FilterOperator::IN => $query->whereIn($filter->field, $filter->value),
            FilterOperator::NOT_IN => $query->whereNotIn($filter->field, $filter->value),
            FilterOperator::IS_NULL => $query->whereNull($filter->field),
            FilterOperator::IS_NOT_NULL => $query->whereNotNull($filter->field),
            FilterOperator::BETWEEN => $query->whereBetween($filter->field, $filter->value),
        };
    }
}
