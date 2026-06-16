<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\ClientDataTable;
use Illuminate\Http\Request;
use App\Models\{Client,Company};

interface ClientRepositoryInterface {
    public function index(ClientDataTable $clientDataTable);
    public function create();
    public function store(Request $request);
    public function edit($id);
    public function update(Request $request, $id);
    public function destroy(Client $client);
    public function storeCompany(Request $request, Client $client);
    public function updateCompanyStatus(Request $request, Client $client, Company $company);
}