<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\FeatureDataTable;
use App\Repositories\Contracts\FeatureRepositoryInterface;
use App\Models\Feature;
use App\Http\Requests\Dashboard\Feature\StoreFeatureRequest;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function __construct(
        protected FeatureDataTable $featureDataTable,
        protected FeatureRepositoryInterface $featureInterface
    ) {}

    public function index()
    {
        return $this->featureInterface->index($this->featureDataTable);
    }

    public function store(StoreFeatureRequest $request)
    {
        return $this->featureInterface->store($request);
    }

    public function edit(Feature $feature)
    {
        $feature->load('translations');

        return response()->json([
            'success' => true,
            'data'    => $feature,
        ]);
    }

    public function update(Request $request, Feature $feature)
    {
        return $this->featureInterface->update($feature, $request->all());
    }

    public function toggleStatus(Feature $feature)
    {
        return $this->featureInterface->toggleStatus($feature);
    }

    public function toggleScope(Feature $feature)
    {
        return $this->featureInterface->toggleScope($feature);
    }

    public function destroy(Feature $feature)
    {
        return $this->featureInterface->destroy($feature);
    }
}