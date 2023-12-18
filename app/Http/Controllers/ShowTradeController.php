<?php

namespace App\Http\Controllers;

use App\Services\ShareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ShowTradeController extends Controller
{

    public $dict_figi;

    public function getFigiByTicker($ticker) {
        $this->dict_figi = ShareService::getFigi();
        $figi = $this->dict_figi[$ticker];
        return $figi;
    }

    public function getTradeByPrices($figi): array {
        return Redis::hGetAll("BUY:$figi");
    }

    public function getTradeSellPrices($figi) {
    return Redis::hGetAll("SELL:$figi");
         

    }
}
