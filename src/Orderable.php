<?php

namespace SnowRunescape\Orderable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

trait Orderable
{
    public static function bootOrderable()
    {
        static::addGlobalScope(new OrderableScope);

        static::creating(function ($model) {
            if ($model->shouldSortWhenCreating()) {
                $orderColumn = $model->getOrderColumnName();
                $maxOrder = $model->max($orderColumn) + 1;

                $model->{$orderColumn} = $maxOrder;
            }
        });
    }

    public function getOrderColumnName(): string
    {
        return $this->sortable["column_name"] ?? "order";
    }

    public function getSortDirection(): string
    {
        return $this->orderableOptions["sort_direction"] ?? "ASC";
    }

    public function getScopeColumns(): array
    {
        return $this->sortable["scope_columns"] ?? [];
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
        $scopeColumns = $model->getScopeColumns();
        $beforeOrder = $order - 1;

        $query = self::query();

        foreach ($scopeColumns as $column) {
            $query->where($column, $model->{$column});
        }

        DB::statement("SET @cnt = -1");

        $query->orderBy($model->getOrderColumnName(), $model->getSortDirection())
            ->update([
                $orderColumn => self::raw("(CASE
                    WHEN `{$model->getKeyName()}` = {$model->getKey()} THEN {$order}
                    WHEN @cnt = {$beforeOrder} THEN @cnt := @cnt + 2
                    ELSE @cnt := @cnt + 1
                END)")
            ]);
    }
}
