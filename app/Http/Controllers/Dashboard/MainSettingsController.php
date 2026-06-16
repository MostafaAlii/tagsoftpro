<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\MainSettingRequest;
use App\Repositories\Contracts\MainSettingRepositoryInterface;

class MainSettingsController extends Controller
{
    public function __construct(protected MainSettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }
    public function index()
    {
        return $this->settingRepository->index();
    }

    public function store(MainSettingRequest $request)
    {
        return $this->settingRepository->save($request);
    }
}