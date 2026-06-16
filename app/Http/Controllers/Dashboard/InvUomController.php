<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\InvUomDataTable;
use App\Repositories\Contracts\InvUomRepositoryInterface;
use App\Models\InvUom;
use App\Http\Requests\Dashboard\InvUom\StoreInvUomRequest;
use Illuminate\Http\Request;
class InvUomController extends Controller
{
    public function __construct(
        protected InvUomDataTable $invUomDataTable,
        protected InvUomRepositoryInterface $invUomInterface
    ) {}

    public function index()
    {
        return $this->invUomInterface->index($this->invUomDataTable);
    }

    public function store(StoreInvUomRequest $request)
    {
        return $this->invUomInterface->store($request);
    }

    public function edit(InvUom $invUom)
    {
        $invUom->load('translations');
        return response()->json([
            'success' => true,
            'data' => $invUom
        ]);
    }

    public function update(Request $request, InvUom $invUom)
    {
        return $this->invUomInterface->update($invUom, $request->all());
    }

    public function toggleStatus(InvUom $invUom)
    {
        return $this->invUomInterface->toggleStatus($invUom);
    }

    public function toggleMaster(InvUom $invUom)
    {
        return $this->invUomInterface->toggleMaster($invUom);
    }

    public function destroy(InvUom $invUom)
    {
        return $this->invUomInterface->destroy($invUom);
    }
}