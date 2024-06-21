<?php

namespace SnowRunescape\Orderable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->ordered();
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        $builder->macro("ordered", function (Builder $builder) {
            return $builder->orderBy(
                $builder->getModel()->getOrderColumnName(),
                $builder->getModel()->getSortDirection()
            );
        });

        $builder->macro("withoutOrdered", function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
