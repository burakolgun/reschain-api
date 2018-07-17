<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getMainPage()
    {
        $date = new Carbon();
        $today = $date::now();
        $pastTime = clone $today->subDays(45);
        $nextTime = clone $today->addDays(90);

        $calendar = [];

        for($time = clone $nextTime; $time->greaterThan($pastTime); $pastTime->addDay()) {
            $calendar[] = $pastTime->toDateString();
        }

        return response()->json($calendar);
    }
}
