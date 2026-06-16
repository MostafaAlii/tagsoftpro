<?php

namespace  App\Repositories\Eloquents;

use App\Models\{Client,Company};
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Http\Request;
use App\DataTables\Dashboard\Admin\ClientDataTable;
use App\Enums\Client\ClientStatus;

class ClientRepository implements ClientRepositoryInterface
{
    public function index(ClientDataTable $clientDataTable)
    {
        return $clientDataTable->render('dashboard.admin.clients.index', ['title' => 'العملاء']);
    }

    public function create()
    {
        return view('dashboard.admin.clients.btn.create', ['title' => 'اضافه عميل']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email|unique:clients,email',
        ]);
        Client::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->phone,
            'status'   => ClientStatus::ACTIVE->value,
        ]);
        return redirect()->route('admin.clients.index')->with('success', 'تم حفظ العميل بنجاح!');
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('dashboard.admin.clients.btn.edit', ['client' => $client, 'title' => 'تعديل العميل']);
    }

    public function update(Request $request, $id)
    { 
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email'  => 'nullable|email|unique:clients,email,' . $id,
            'status' => 'required|in:' . implode(',', array_column(\App\Enums\Client\ClientStatus::cases(), 'value'))
        ]);
        $client = Client::findOrFail($id);
        $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);
        return redirect()->route('admin.clients.index')->with('success', 'تم تحديث العميل بنجاح!');
    }

    public function destroy(Client $client) {
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'تم الحذف بنجاح!');
    }

    public function storeCompany(Request $request, Client $client) {
        $request->validate([
            'name'  => 'required|string|max:255|unique:companies,name',
            'email' => 'nullable|email|unique:companies,email',
            'phone' => 'nullable|string',
        ]);

        $client->companies()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => bcrypt($request->phone),
            'status'   => 'active',
        ]);
        return redirect()->route('admin.clients.index')->with('success', 'تم إضافة الشركة بنجاح!');
    }

    public function updateCompanyStatus(Request $request, Client $client, Company $company) {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_column(\App\Enums\Company\CompanyStatus::cases(), 'value')),
        ]);
        $company->update(['status' => $request->status]);
        return response()->json([
            'success'   => true,
            'message'   => trans('dashboard/company.status_updated'),
            'rowClass'  => \App\Enums\Company\CompanyStatus::rowClass($request->status),
        ]);
    }
}