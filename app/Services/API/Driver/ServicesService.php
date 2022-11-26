<?php


namespace App\Services\API\Driver;


use App\Models\Service;
use App\Models\DriverService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicesService
{
    public $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        $data = Service::select('name', 'id', 'image', 'description')->get();

        if (sizeof($data) > 0) {
            return makeResponse('success', 'Service Fetch Successfully', 200, $data);
        } else {
            return makeResponse('error', 'No Record Found', 404);
        }
    }

    public function save($request)
    {
        DB::beginTransaction();
        try {
            DriverService::where('user_id',Auth::user()->id)->delete();
            foreach ($request->services as $service) {
                $save = DriverService::create(['user_id' => Auth::user()->id, 'service_id' => $service]);
            }
            Auth::user()->steps = 3;
            Auth::user()->user_type = 4;
            Auth::user()->save();

            DB::commit();

            $data = $this->authService->loginUserResponse();

            return makeResponse('success', 'Driver Service Save Successfully', 200,$data);
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in saving driver response: ' . $e, 500);
        }
    }


}
