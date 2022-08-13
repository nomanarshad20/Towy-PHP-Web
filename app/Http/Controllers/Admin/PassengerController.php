<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PassengerSaveRequest;
use App\Models\User;
use App\Services\Admin\PassengerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    //

    public $passengerService;
    public function __construct(PassengerService $passengerService)
    {
        $this->passengerService = $passengerService;
    }

    public function index()
    {
        return $this->passengerService->index();
    }

    public function create()
    {
        return $this->passengerService->create();
    }

    public function save(PassengerSaveRequest $request)
    {
        $this->validate($request,[
            'password' => 'required|min:8'
        ]);

        $user = new User;
        return $this->passengerService->save($user,$request);
    }

    public function edit($id)
    {
        return $this->passengerService->edit($id);

    }

    public function update(PassengerSaveRequest $request)
    {
        if($request->password)
        {
            $this->validate($request,[
                'password' => 'required|min:8'
            ]);
        }

        $user =  User::find($request->id);
        return $this->passengerService->save($user,$request);



    }

    public function delete(Request $request)
    {
        return $this->passengerService->delete($request);
    }

    public function changeStatus(Request $request)
    {
        return $this->passengerService->changeStatus($request);
    }
}
