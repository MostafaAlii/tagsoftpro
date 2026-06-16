<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\Dashboard\MainSettingRequest;

interface MainSettingRepositoryInterface
{
    public function index();
    public function save(MainSettingRequest $request);
}