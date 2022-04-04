<?php

namespace App\Interfaces;

/**
 * for models with status column.
 */
interface IHasStatusColumn
{
    public static function getDefaultStatus(): int;

    /**
     * returns translation for given/model status.
     *
     * @param int $status
     *
     * @return string
     */
    public static function getStatusLabel(int $status): string;

    /**
     * returns translated statuses list.
     *
     * @return array|null
     */
    public static function getAllStatuses(): ?array;

    /**
     * handle $model->status_label
     *
     * @return mixed
     */
    public function getStatusLabelAttribute();
}
