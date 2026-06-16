<?php
namespace App\Repositories\Contracts;
use App\DataTables\Dashboard\Admin\FeatureDataTable;
use App\Models\Feature;
use App\Http\Requests\Dashboard\Feature\StoreFeatureRequest;
interface FeatureRepositoryInterface {
    public function index(FeatureDataTable $featureDataTable);
    public function store(StoreFeatureRequest $request);
    public function update(Feature $feature, array $data);
    public function toggleStatus(Feature $feature);
    public function toggleScope(Feature $feature);
    public function destroy(Feature $feature);
}