<?php 
namespace App\Services;

use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use Tinkoff\Invest\V1\GetLastTradesRequest;
use Tinkoff\Invest\V1\InstrumentsRequest;
use Tinkoff\Invest\V1\InstrumentStatus;
use Tinkoff\Invest\V1\Instrument;
use Tinkoff\Invest\V1\InstrumentIdType;
use Tinkoff\Invest\V1\InstrumentType;
use App\Services\ShareService;

class LastHourTrade {

    private $service;
    public $name;

    public $ticker;
    public $sum_trades_buy = [];
    public $sum_trades_sell = [];
    public $figi;

    public $trades;



    public function __construct() {
        $this->service = new ShareService;
    }

    public function setTicker($ticker) : void {
        $this->ticker = $ticker;
    }

    public function getTicker() : string {
        return $this->ticker;
    }

    public  function getFigi() : string {
        $ticker_to_figi = $this->service->getFigi();
        $this->figi = $ticker_to_figi[self::getTicker()];
        return $this->figi;
    } 

    public function getNames() : string {
        $names = $this->service->getNames();        
        $this->name = $names[self::getFigi()];
        return $this->name;
    }
    

    public  function getLastHourTrades() {

        $factory = TinkoffClientsFactory::create($_ENV['TOKEN']); 
        $request = new GetLastTradesRequest();
        $request->setFigi(self::getFigi());
        list($response) = $factory->marketDataServiceClient->GetLastTrades($request)->wait();
        $this->trades = $response->getTrades();
        return $this->trades;
    }

    public function getCountBuy($price) {
        if(array_key_exists($price, $this->sum_trades_buy)) {
            return $this->sum_trades_buy[$price];
        }
    }
    public function getCountSell($price) {
        if(array_key_exists($price, $this->sum_trades_sell)) {
            return $this->sum_trades_sell[$price];
        } else {
            return 0;
        }
    }

    public function getBuyPrices() {
        $trades = self::getLastHourTrades();
        foreach ($trades as $trade) {
            if($trade->getDirection() === 1) {
                $key = $trade->getPrice()
                    ->getUnits() + $trade->getPrice()
                    ->getNano() / pow(10, 9);
                $value = $trade->getQuantity() + self::getCountBuy($key);
                settype($key, 'string');
                $this->sum_trades_buy[$key] = $value;
                unset($key, $value);
            }
        }
        return $this->sum_trades_buy;
    }
    public function getSellPrices() {
        $trades = self::getLastHourTrades();
        foreach ($trades as $trade) {
            if($trade->getDirection() === 2) {
                $key = $trade->getPrice()
                    ->getUnits() + $trade->getPrice()
                    ->getNano() / pow(10, 9);
                $value = $trade->getQuantity() + self::getCountSell($key);
                settype($key, 'string');
                $this->sum_trades_sell[$key] = $value;
                unset($key, $value);
            }
        }
        return $this->sum_trades_sell;
    }

}
