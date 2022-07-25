<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\CreateRatingRequest;
use App\Services\API\Passenger\RatingService;
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
