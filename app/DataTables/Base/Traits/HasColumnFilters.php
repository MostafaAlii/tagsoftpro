<?php

namespace App\DataTables\Base\Traits;

trait HasColumnFilters
{
    /**
     * Add text filter to a column
     */
    protected function addTextFilter($dataTable, $column, $queryCallback = null)
    {
        return $dataTable->filterColumn($column, function ($query, $keyword) use ($queryCallback) {
            if ($keyword) {
                if ($queryCallback) {
                    $queryCallback($query, $keyword);
                } else {
                    $query->where($column, 'like', "%{$keyword}%");
                }
            }
        });
    }

    /**
     * Add select filter to a column
     */
    protected function addSelectFilter($dataTable, $column, $callback = null)
    {
        return $dataTable->filterColumn($column, function ($query, $keyword) use ($callback) {
            if ($keyword !== '') {
                if ($callback) {
                    $callback($query, $keyword);
                } else {
                    $query->where($column, (int) $keyword);
                }
            }
        });
    }

    /**
     * Generate filter input HTML
     */
    protected function generateFilterInput($type = 'text', $placeholder = '', $options = [])
    {
        if ($type === 'select') {
            $select = '<select class="mt-1 form-select form-select-sm">';
            $select .= '<option value="">' . trans('dashboard/general.all') . '</option>';
            foreach ($options as $value => $label) {
                $select .= "<option value=\"{$value}\">{$label}</option>";
            }
            $select .= '</select>';
            return $select;
        }

        return '<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . $placeholder . '">';
    }
}
