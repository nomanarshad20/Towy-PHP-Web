<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerImageRequest;
use App\Services\Admin\BannerImageService;
use Illuminate\Http\Request;

class BannerImageController extends Controller
{
    public $bannerImage;
    public function __construct(BannerImageService $bannerImageService)
    {
        $this->bannerImage = $bannerImageService;
    }

    public function index()
    {
        return $this->bannerImage->index();
    }

    public function create()
    {
        return $this->bannerImage->create();
    }

    public function save(BannerImageRequest $request)
    {
        return $this->bannerImage->save($request);
    }

    public function delete(Request $request)
    {
        return $this->bannerImage->delete($request);
    }

    public function changeStatus(Request $request)
    {
        return $this->bannerImage->changeStatus($request);
    }

}

