<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CreateBookingRequest;
use App\Http\Requests\API\Driver\CreateRatingRequest;
use App\Services\API\Driver\RatingService;
use Illuminate\Http\Request;

class BookingRatingController extends Controller
{

    public $ratingService;
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    public function giveRating(CreateRatingRequest $request)
    {
        return $this->ratingService->saveRating($request);
    }
}
