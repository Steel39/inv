<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use Tinkoff\Invest\V1\InstrumentRequest;
use Tinkoff\Invest\V1\InstrumentsRequest;
use Tinkoff\Invest\V1\InstrumentStatus;
use Tinkoff\Invest\V1\Instrument;
use Tinkoff\Invest\V1\MarketDataRequest;
use Tinkoff\Invest\V1\MarketDataResponse;
use Tinkoff\Invest\V1\SubscribeTradesRequest;
use Tinkoff\Invest\V1\SubscriptionAction;
use Tinkoff\Invest\V1\TradeInstrument;
use App\Services\TradeDataStore;
use Tinkoff\Invest\V1\Trade;

class Trades extends Controller
{
    private $token;
    protected $factory;
    protected $subscription;
    protected $store;



    public function __construct()
    {
        $this->token = $_ENV['TOKEN'];
    }

    public function connect()
    {
        $this->store = new TradeDataStore();

        $this->factory = TinkoffClientsFactory::create($this->token);
        $instruments_request = new InstrumentsRequest();
        $instruments_request->setInstrumentStatus(InstrumentStatus::INSTRUMENT_STATUS_ALL);

        list($response) = $this->factory->instrumentsServiceClient->Shares($instruments_request)
            ->wait();

        /** @var Instrument[] $instruments_dict */
        $instruments_dict = $response->getInstruments();
        $meta_instruments = [];
        $names = [];

        foreach ($instruments_dict as $instrument) {
            if($instrument->getTradingStatus() === 5 && $instrument->getCountryOfRisk() === 'RU') {
                $item = (new TradeInstrument())->setFigi($instrument->getFigi());
                $name = $instrument->getName();
                $figi = $instrument->getFigi();
                $names[$figi] =  $name;
                array_push($meta_instruments, $item);
            }
        }
        if (empty($meta_instruments)) {
            echo('Instrument not found');

            die();
        }

        $this->subscription = (new MarketDataRequest())
        ->setSubscribeTradesRequest(
            (new SubscribeTradesRequest())
            ->setSubscriptionAction(SubscriptionAction::SUBSCRIPTION_ACTION_SUBSCRIBE)
            ->setInstruments($meta_instruments)
        );

        $stream = $this->factory->marketDataStreamServiceClient->MarketDataStream();
        $stream->write($this->subscription);

/**
 * @var MarketDataResponse $market_data_response
 */

        while($market_data_response = $stream->read()) {
            if($trades = $market_data_response->getTrade()){
                $trade_dir = $trades->getDirection();
                $trade_figi = $trades->getFigi();
                $trade_count = $trades->getQuantity();
                $trade_price = $trades->getPrice()
                ->getUnits() + $trades->getPrice()
                ->getNano() / pow(10, 9);
                $ratio = 0.000005;
                $this->store->getVolumeTrade($trade_figi, $trade_count, $trade_dir, $trade_price);
            };
        };
    }
}
