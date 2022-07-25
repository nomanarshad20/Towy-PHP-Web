<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Services\API\Passenger\BannerImageService;
use Illuminate\Http\Request;

class BannerImageController extends Controller
{
    public $bannerImageService;
    public function __construct(BannerImageService $bannerImageService)
    {
        $this->bannerImageService = $bannerImageService;
    }

    public function index()
    {
        return $this->bannerImageService->index();
    }
}
