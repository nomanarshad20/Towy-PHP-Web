@component('mail::message')


@component('mail::table')

    | Receipt of Tow Service      |          |
    | :--------- | :------------- |
    | Total Distance       | {{$booking->total_distance }}        |
    | Waiting Time       | {{$booking->waiting_time ? $booking->waiting_time:0}}          |
    | Total Time       | {{$booking->total_ride_time ? $booking->total_ride_time:0}}         |
    | Total Fare       | $ {{$booking->actual_fare}}         |


@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
