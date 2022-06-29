<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePeakFactor;
use App\Http\Requests\Admin\SavePeakFactorRequest;
use App\Http\Requests\Admin\SaveSettingRequest;
use App\Services\Admin\PeakService;
use Illuminate\Http\Request;

class PeakFactorController extends Controller
{
    public $peakService;

    public function __construct(PeakService $peakService)
    {
        $this->peakService = $peakService;
    }

    public function index()
    {
        return $this->peakService->index();
    }

    public function create()
    {
        return $this->peakService->create();
    }

    public function save(SavePeakFactorRequest $request)
    {
        return $this->peakService->save($request);
    }

    public function edit($id)
    {
        return $this->peakService->edit($id);
    }

    public function update(SavePeakFactorRequest $request)
    {
        return $this->peakService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->peakService->delete($request);
    }
}
