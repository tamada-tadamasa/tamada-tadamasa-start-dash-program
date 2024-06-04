<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Consts\Flag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope as TimestampsSoftDeletingScope;

/**
 * The Scope to use Overwrite SoftDelete Trait.
 *
 * @author Yasuhiro Kihara
 */
class SoftDeletingScope extends TimestampsSoftDeletingScope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->getQualifiedDeletedAtColumn(), Flag::FALSE);
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);

            return $builder->update([
                $column => Flag::TRUE,
                'deleted_at' => now(),
            ]);
        });
    }

    /**
     * Get the "is deleted" column for the builder.
     *
     * @return string
     */
    protected function getDeletedAtColumn(Builder $builder)
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedDeletedAtColumn();
        }

        return $builder->getModel()->getDeletedAtColumn();
    }

    /**
     * Add the restore extension to the builder.
     */
    protected function addRestore(Builder $builder): void
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();

            return $builder->update([$builder->getModel()->getDeletedAtColumn() => Flag::FALSE, 'deleted_at' => null]);
        });
    }

    /**
     * Add the with-trashed extension to the builder.
     */
    protected function addWithTrashed(Builder $builder): void
    {
        $builder->macro('withTrashed', function (Builder $builder, $withTrashed = true) {
            if (!$withTrashed) {
                return $builder->withoutTrashed();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-trashed extension to the builder.
     */
    protected function addWithoutTrashed(Builder $builder): void
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedDeletedAtColumn(),
                Flag::FALSE
            );

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     */
    protected function addOnlyTrashed(Builder $builder): void
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedDeletedAtColumn(),
                Flag::TRUE
            );

            return $builder;
        });
    }
}
