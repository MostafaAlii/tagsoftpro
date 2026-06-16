<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class ClientDataTable extends BaseDataTable
{
    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Client);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (Client $client) {
                return view('dashboard.admin.clients.btn.actions', compact('client'));
            })
            ->editColumn('status', function (Client $client) {
                return \App\Enums\Client\ClientStatus::badge($client->status?->value);
            })
            ->addColumn('companies', function (Client $client) {
                if ($client->companies_count === 0) {
                    return view('dashboard.admin.clients.btn.add_company', compact('client'))->render();
                }
                return view('dashboard.admin.clients.btn.companies_count', compact('client'))->render();
            })
            ->editColumn('created_at', function (Client $client) {
                return $this->formatTranslatedDate($client->created_at);
            })
            ->editColumn('updated_at', function (Client $client) {
                return $this->formatTranslatedDate($client->updated_at);
            })
            ->rawColumns(['action','status', 'companies', 'created_at', 'updated_at']);
    }

    public function query(): QueryBuilder {
        return Client::withCount('companies')->latest();
    }

    public function getColumns(): array
    {
        return [
            ['name' => 'id', 'data' => 'id', 'title' => '#', 'className' => 'text-center'],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/client.name'), 'className' => 'text-center'],
            ['name' => 'status','data' => 'status','title' => trans('dashboard/general.status'),'orderable' => false, 'searchable' => false,'className' => 'text-center'],
            ['name' => 'companies', 'data' => 'companies', 'title' => trans('dashboard/client.companies'), 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
            ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center'],
            ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center'],
            ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
        ];
    }
}