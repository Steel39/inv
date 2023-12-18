<?php

namespace App\Services;

use App\Events\DataPushed;
use App\Http\Controllers\Controller;
use App\Services\ShareService;
use Illuminate\Support\Facades\Redis;
use Tinkoff\Invest\V1\Share;

class GetDataTrades {

    public $dataTrades;

    private $sell;
    private $buy;

    private $sell_count;

    private $buy_count;
    private $names;

    protected function getNames() {
        $this->names = ShareService::getNames();
        return $this->names;
    }


    protected function getSellCount($figi): mixed {

        $this->sell_count = Redis::get("-" . $figi);
        return $this->sell;
    }


    public function getDataSellTrades() {
        foreach(self::getNames() as $key=>$value) {
            $this->sell_count[$key] = Redis::get('-' . $key);
        }

        return $this->sell_count;
    }
     public function getDataBuyTrades() {

        foreach(self::getNames() as $key=>$value) {
            $this->buy_count[$key] = Redis::get('+' . $key);
        }
        return $this->buy_count;
    }


}
