<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class FinancialYearDataTable extends BaseDataTable
{
    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new FinancialYear);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (FinancialYear $financialYear) {
                return view('dashboard.admin.financial.financialYear.btn.actions', compact('financialYear'));
            })
            ->addColumn('months_count', function (FinancialYear $financialYear) {
                return $financialYear->months->count() ?: trans('dashboard/financial_year.no_months');
            })
            ->addColumn('months_status', function (FinancialYear $financialYear) {
                $closed = $financialYear->months->where('is_closed', 1)->count();
                $open   = $financialYear->months->where('is_closed', 0)->count();
                return trans('dashboard/financial_year.closed') . ': ' . $closed . ' | ' .
                    trans('dashboard/financial_year.open') . ': ' . $open;
            })
            ->addColumn('open_month_name', function (FinancialYear $financialYear) {
                $openMonth = $financialYear->months->where('is_closed', 0)->first();
                return $openMonth?->name ?? trans('dashboard/financial_year.no_months');
            })
            ->addColumn('responsible', function (FinancialYear $financialYear) {
                $addedBy   = $financialYear->addedBy?->name ? "✍️ " . $financialYear->addedBy->name : null;
                $updatedBy = $financialYear->updatedBy?->name ? "📝 " . $financialYear->updatedBy->name : null;
                return $addedBy || $updatedBy ? implode(' | ', array_filter([$addedBy, $updatedBy])) : '-';
            })
            ->addColumn('display_name', function (FinancialYear $financialYear) {
                return $financialYear->display_name;
            })
            ->editColumn('is_active_label', function (FinancialYear $financialYear) {
                return $financialYear->is_active_label;
            })
            ->editColumn('created_at', function (FinancialYear $financialYear) {
                return $this->formatTranslatedDate($financialYear->created_at);
            })
            ->editColumn('updated_at', function (FinancialYear $financialYear) {
                return $this->formatTranslatedDate($financialYear->updated_at);
            })
            ->editColumn('start_date', function (FinancialYear $financialYear) {
                return $this->formatTranslatedDate($financialYear->start_date);
            })
            ->editColumn('end_date', function (FinancialYear $financialYear) {
                return $this->formatTranslatedDate($financialYear->end_date);
            })
            ->rawColumns(['action', 'is_active_label', 'months_count', 'months_status', 'open_month_name', 'responsible', 'created_at', 'updated_at', 'start_date', 'end_date']);
    }

    public function query(): QueryBuilder
    {
        $user = get_user_data()?->company_id;
        return FinancialYear::where('company_id', $user)->latest();
    }

    public function getColumns(): array
    {
        return [
            ['name' => 'id', 'data' => 'id', 'title' => '#'],
            ['name' => 'name', 'data' => 'display_name', 'title' => trans('dashboard/financial_year.name')],
            ['name' => 'start_date', 'data' => 'start_date', 'title' => trans('dashboard/financial_year.start_date')],
            ['name' => 'end_date', 'data' => 'end_date', 'title' => trans('dashboard/financial_year.end_date')],
            ['name' => 'is_active', 'data' => 'is_active_label', 'title' => trans('dashboard/financial_year.is_active')],
            ['name' => 'months_count', 'data' => 'months_count', 'title' => trans('dashboard/financial_year.months_count')],
            ['name' => 'months_status', 'data' => 'months_status', 'title' => trans('dashboard/financial_year.months_status')],
            ['name' => 'open_month_name', 'data' => 'open_month_name', 'title' => trans('dashboard/financial_year.open_month')],
            ['name' => 'responsible', 'data' => 'responsible', 'title' => trans('dashboard/financial_year.responsible')],
            ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at')],
            ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at')],
            ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'orderable' => false, 'searchable' => false],
        ];
    }
}