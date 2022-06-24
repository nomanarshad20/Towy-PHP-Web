<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FranchiseCreateRequest;
use App\Services\Admin\FranchiseService;
use Illuminate\Http\Request;

class FranchiseController extends Controller
{

    public $franchiseService;

    public function __construct(FranchiseService $franchiseService) {
        $this->franchiseService = $franchiseService;
    }

    public function index()
    {
        return $this->franchiseService->index();
    }

    public function create()
    {
        return $this->franchiseService->create();
    }

    public function save(FranchiseCreateRequest $request)
    {
        $this->validate($request,[
            'mobile_no' => 'required|unique:users',
            'email' => 'email|unique:users',
            'password' => 'required|min:8'

        ]);

        return $this->franchiseService->save($request);
    }

    public function edit($id)
    {
        return $this->franchiseService->edit($id);
    }

    public function update(FranchiseCreateRequest $request)
    {
        $this->validate($request,[
            'password' => 'nullable|min:8',
            'email' => 'required|email',
            'mobile_no' => 'required'
        ]);

        return $this->franchiseService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->franchiseService->delete($request);
    }

    public function changeStatus(Request $request)
    {
        return $this->franchiseService->changeStatus($request);
    }
}
