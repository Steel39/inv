<?php

namespace App\Http\Controllers;

use App\Services\GetDataTrades;
use App\Services\LastHourTrade;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(){
        $test = new GetDataTrades;

    }
}
