<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\Dashboard\Admin\ClientDataTable;
use App\Models\{Client,Company};
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientController extends Controller {
    public function __construct(protected ClientDataTable $clientDataTable, protected ClientRepositoryInterface $clientInterface) {
        $this->clientInterface = $clientInterface;
        $this->clientDataTable = $clientDataTable;
    }

    public function index(ClientDataTable $clientDataTable) {
        return $this->clientInterface->index($this->clientDataTable);
    }

    public function create() {
        return $this->clientInterface->create();
    }

    public function store(Request $request) {
        return $this->clientInterface->store($request);
    }

    public function storeCompany(Request $request, Client $client) {
        return $this->clientInterface->storeCompany($request, $client);
    }

    public function edit($id) {
        return $this->clientInterface->edit($id);
    }

    public function update(Request $request, $id) {
        return $this->clientInterface->update($request, $id);
    }

    public function destroy(Client $client) {
        return $this->clientInterface->destroy($client);
    }

    public function updateCompanyStatus(Request $request, Client $client, Company $company) {
        return $this->clientInterface->updateCompanyStatus($request, $client, $company);
    }
}