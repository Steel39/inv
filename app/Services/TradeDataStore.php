<?php 

namespace App\Services;

use App\Events\DataPushed;
use App\Http\Controllers\Controller;
use App\Services\ShareService;
use Illuminate\Support\Facades\Redis;

class TradeDataStore extends Controller 
{

    public $names;
    public $issues;

    public $lots;

    public $ratio = 0.000005;

    public function __construct() {
        $this->names = ShareService::getNames();
        $this->issues = ShareService::getIssues(); 
        $this->lots = ShareService::getLots(); 
    }


    public function setTrade(string $figi, int $count, int $direction, $ratio) {

        if ( $direction === 1 && empty(Redis::get('+' . $figi)) ) {
            $index = '+' . $figi;
        Redis::set($index, $count, 'EX', 60);
        } elseif ($direction === 1 && !empty(Redis::get('+' . $figi))) {
            $index = '+' . $figi;
            $lot_count = Redis::get('+' . $figi) + $count;
            Redis::set($index, $lot_count);
            if($lot_count > $this->issues[$figi]*$ratio ) {
                echo  'BUY  ' . $this->names[$figi] . PHP_EOL . PHP_EOL; 
                Redis::del('+' . $figi);
            }
            unset($index, $lot_count, $figi);    
        } elseif ($direction === 2 && empty(Redis::get('-' . $figi))){
            $index = '-' . $figi;
            Redis::set($index, $count, 'EX', 60);
        } elseif ($direction === 2 && !empty(Redis::get('-' . $figi))) {
            $index = '-' . $figi;
            $lot_count = Redis::get('-' . $figi) + $count;
            Redis::set($index, $lot_count);
            if($lot_count > $this->issues[$figi]*$ratio ) {
                echo  'SELL  ' . $this->names[$figi] . PHP_EOL . PHP_EOL; 
                Redis::del('-' . $figi);
            }
            unset($index, $lot_count, $figi);
        }
    }

    public function getVolumeTrade(string $figi, int $count, int $direction, float $price) {
        $trade_buy = Redis::get('+' . $figi);
        $trade_sell = Redis::get('-' . $figi);   
       
        if($direction === 1 && empty($trade_buy)){
            Redis::set('+' . $figi, $count);
        } elseif($direction === 1 && !empty($trade_buy)) {
            $set_count = ($trade_buy + $count);
            Redis::set('+' . $figi, $set_count);  
            echo 'BUY' . PHP_EOL;  
            unset($set_count);
        } elseif($direction === 2 && empty($trade_sell)) {
            Redis::set('-' . $figi, $count);
        } elseif($direction === 2 && !empty($trade_sell)) {
            $set_count = ($trade_sell + $count);
            Redis::set('-' . $figi, $set_count);
            echo 'SELL' . PHP_EOL;  
            unset($set_count);
            }
            echo 'Куплено ' . $this->names[$figi] . ' - ' . $trade_buy . PHP_EOL;
            echo 'Продано ' . $this->names[$figi] . ' - ' . $trade_sell . PHP_EOL . PHP_EOL;
            unset($trade_buy, $trade_sell);
    }

    public function getPriceTrade($figi, $count, $direction, $price) {

        if($direction === 1) {
            $hash_count = Redis::hGet("BUY:$figi", $price);
            $set_count = $count + $hash_count;
            Redis::hSet("BUY:$figi",$price, $set_count);
            event(new DataPushed($this->names[$figi]), $set_count, $price);
            echo "куплено" . $this->names[$figi] . PHP_EOL . $price . ' - ' . $set_count . PHP_EOL . PHP_EOL;


        } elseif($direction === 2) {
            $hash_count = Redis::hGet("SELL:$figi", $price);
            $set_count = $count + $hash_count;
            
            Redis::hSet("SELL:$figi", $price, $set_count);
            event(new DataPushed($price));
            echo "продано" . $this->names[$figi] . PHP_EOL . $price . ' - ' . $set_count . PHP_EOL . PHP_EOL;
        }

        
        
    }
}
