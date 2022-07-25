<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Services\API\Passenger\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService =  $notificationService;
    }

    public function index()
    {
        return $this->notificationService->index();
    }
}
