<?php

namespace App\DataTables\Base;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Carbon\Carbon;

abstract class BaseDataTable extends DataTable
{
    protected $customFilters = []; // لتخزين الفلاتر المخصصة

    public function __construct(protected Model $model)
    {
        $model = $this->model;
    }

    abstract protected function dataTable(QueryBuilder $query): EloquentDataTable;

    abstract protected function query();

    public function html(): HtmlBuilder
    {
        $html = $this->builder()
            ->setTableId($this->model->getTable() . '_datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getParameters());

        // إضافة الفلاتر المخصصة إذا وجدت
        if (!empty($this->customFilters)) {
            $html = $this->addCustomFilters($html);
        }

        return $html;
    }

    abstract protected function getColumns(): array;

    /**
     * تعريف الفلاتر المخصصة (يتم تجاوزها في الـ DataTable الفرعية)
     */
    protected function getCustomFilters(): array
    {
        return [];
    }

    /**
     * إضافة الفلاتر المخصصة إلى الـ DataTable
     */
    protected function addCustomFilters($html)
    {
        $initCompleteScript = $this->generateInitCompleteScript();

        $parameters = $html->getParameters();
        if (isset($parameters['initComplete'])) {
            $initCompleteScript = $parameters['initComplete'] . $initCompleteScript;
        }

        return $html->parameters(array_merge($parameters, [
            'initComplete' => $initCompleteScript,
        ]));
    }

    /**
     * توليد سكربت الفلاتر المخصصة
     */
    protected function generateInitCompleteScript()
    {
        $filters = $this->getCustomFilters();
        if (empty($filters)) {
            return '';
        }

        $script = 'function() {
            var api = this.api();
        ';

        foreach ($filters as $filter) {
            $script .= "
            // فلتر عمود: {$filter['name']}
            var colIndex_{$filter['name']} = {$filter['column_index']};
            var header_{$filter['name']} = $(api.column(colIndex_{$filter['name']}).header());
            var input_{$filter['name']} = $(\"{$filter['html']}\");
            header_{$filter['name']}.append(input_{$filter['name']});
            
            input_{$filter['name']}.on(\"{$filter['event']}\", function(e) {
                e.stopPropagation();
                api.column(colIndex_{$filter['name']}).search(this.value).draw();
            });
            
            input_{$filter['name']}.on(\"click\", function(e) {
                e.stopPropagation();
            });
            ";
        }

        $script .= '}';
        return $script;
    }

    protected function getParameters()
    {
        return [
            'dom' => 'Blfrtip',
            'lengthMenu' => [
                [10, 25, 50, 100, 500, 750, -1],
                ['10', '25 ', '50 ', '100 ', '500 ', '750', trans('dashboard/datatable.all_records')]
            ],
            'buttons' => [
                [
                    'extend' => 'csv',
                    'className' => 'btn btn-primary',
                    'text' => "<i class='fa fa-file'></i>" . trans('dashboard/datatable.ex_csv')
                ],
                [
                    'extend' => 'excel',
                    'className' => 'btn btn-success',
                    'text' => "<i class='fa fa-file'></i>" . trans('dashboard/datatable.ex_excel')
                ],
                [
                    'extend' => 'print',
                    'className' => 'btn btn-info',
                    'text' => "<i class='fa fa-print'></i>" . trans('dashboard/datatable.print')
                ],
            ],
            'language' => datatable_lang(),
        ];
    }

    protected function filename(): string
    {
        return $this->model->getTable() . '_' . date('YmdHis');
    }

    protected function formatBadge($value): string
    {
        $badge = $value == null ? 'danger' : 'success';
        if ($value == null)
            return '<span class="badge badge-' . $badge . '">' . trans('dashboard/datatable.no_date_found') . '</span>';
        return '<span class="badge badge-' . $badge . '">' . $value . '</span>';
    }

    protected function formatStatus($status): string
    {
        $badge = $status == 'active' ? 'success' : 'primary';
        return '<span class="badge badge-' . $badge . '">' . $status . '</span>';
    }

    protected function formatDate($value): string
    {
        return $value ? $value->diffForHumans() : '';
    }

    protected function formatDateOnly($value, $format = 'Y-m-d'): string
    {
        return $value ? $value->format($format) : '';
    }

    protected function formatTranslatedDate($value): string
    {
        if (!$value) {
            return '';
        }
        Carbon::setLocale(app()->getLocale());
        return $value->translatedFormat('d F Y');
    }

    /**
     * Helper method لإضافة filter column بسهولة
     */
    protected function addSearchFilter($dataTable, $column, $callback)
    {
        return $dataTable->filterColumn($column, $callback);
    }
}
