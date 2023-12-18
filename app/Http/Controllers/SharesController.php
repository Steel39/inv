<?php

namespace App\Http\Controllers;

use App\Services\GetDataTrades;
use App\Services\LastHourTrade;
use Illuminate\Http\Request;
use App\Services\ShareService;
use App\Services\TradeDataStore;
use Inertia\Inertia;
use App\Models\Shares;
use Illuminate\Support\Facades\DB;

class SharesController extends Controller
{

    public $names;
    public $buy_count;

    public $sell_count;



    public function index(GetDataTrades $dataTrades)
    {
        $this->names = ShareService::getNames();
        $this->buy_count = $dataTrades->getDataBuyTrades();
        $this->sell_count = $dataTrades->getDataSellTrades();

        foreach ($this->names as $figi=>$name) {
            Shares::firstOrCreate([
                'figi' => $figi,
                'name' => $name,
            ]);
        }

        return Inertia::render('Dashboard', [
            'shares' => $this->names,
            'buyTrades' => $this->buy_count,
            'sellTrades' => $this->sell_count
        ]);
    }

    public function update(GetDataTrades $dataTrades) {


        $this->names = DB::table('shares')->pluck('name', 'figi')->toArray();
        $this->buy_count = $dataTrades->getDataBuyTrades();
        $this->sell_count = $dataTrades->getDataSellTrades();

        return Inertia::render('Dashboard', [
            'shares' => $this->names,
            'buyTrades' => $this->buy_count,
            'sellTrades' => $this->sell_count
        ]);

    }
}
