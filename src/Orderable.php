<?php

namespace SnowRunescape\Orderable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

trait Orderable
{
    public static function bootOrderable()
    {
        static::addGlobalScope(new OrderableScope);

        static::creating(function (Model $model) {
            if ($model->shouldSortWhenCreating()) {
                $orderColumn = $model->getOrderColumnName();

                $query = self::buildOrderQuery($model);

                $maxOrder = $query->max($orderColumn);

                $model->{$orderColumn} = ($maxOrder !== null) ? ($maxOrder + 1) : 0;
            }
        });
    }

    public function getOrderColumnName(): string
    {
        return $this->sortable["column_name"] ?? "order";
    }

    public function getSortDirection(): string
    {
        return $this->sortable["sort_direction"] ?? "ASC";
    }

    public function getScopeColumns(): array
    {
        return $this->sortable["scope_columns"] ?? [];
    }

    public function getWhereConditions(): array
    {
        return $this->sortable["where_conditions"] ?? [];
    }

    public function shouldSortWhenCreating(): bool
    {
        return $this->sortable["sort_when_creating"] ?? true;
    }

    public function shouldApplyGlobalScope(): bool
    {
        return $this->sortable["apply_global_scope"] ?? true;
    }

    public static function updateOrder(Model $model, int $order)
    {
        $orderColumn = $model->getOrderColumnName();
        $beforeOrder = $order - 1;

        $query = self::buildOrderQuery($model);

        DB::statement("SET @cnt = -1");

        $query->ordered()->update([
            $orderColumn => self::raw("(CASE
                WHEN `{$model->getKeyName()}` = {$model->getKey()} THEN {$order}
                WHEN @cnt = {$beforeOrder} THEN @cnt := @cnt + 2
                ELSE @cnt := @cnt + 1
            END)")
        ]);
    }

    private static function buildOrderQuery(Model $model)
    {
        $scopeColumns = $model->getScopeColumns();
        $whereConditions = $model->getWhereConditions();

        $query = self::query();

        foreach ($scopeColumns as $column) {
            $query->where($column, $model->{$column});
        }

        foreach ($whereConditions as $condition) {
            $query->where(...$condition);
        }

        return $query;
    }
}
