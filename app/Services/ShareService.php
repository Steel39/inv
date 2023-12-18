<?php

namespace App\Services;


use App\Http\Controllers\Controller;
use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use Tinkoff\Invest\V1\Instrument;
use Tinkoff\Invest\V1\InstrumentsRequest;
use Tinkoff\Invest\V1\InstrumentStatus;
use Tinkoff\Invest\V1\Share;

class ShareService extends Controller
{




    /**
     * Summary of __construct
     */
    public function __construct() {

    }

    /**
     * Summary of getNames
     * @return array
     */
    public static function getNames(){

        $factory = TinkoffClientsFactory::create($_ENV['TOKEN']);
        $instruments_request = new InstrumentsRequest();
        $instruments_request->setInstrumentStatus(InstrumentStatus::INSTRUMENT_STATUS_ALL);

        list($response) =  $factory->instrumentsServiceClient->Shares($instruments_request)
            ->wait();

        /** @var SharesResponse[] $instruments_dict */
        $instruments_dict = $response->getInstruments();
        $names = [];
        foreach($instruments_dict as $instrument){
            if($instrument->getCountryOfRisk() === 'RU' && $instrument->getTradingStatus() === 5) {
                $key = $instrument->getFigi();
            $value = $instrument->getName();
            $names[$key] = $value;
            }

        }
        return $names;

    }

    

    public static function getFigi() {
        $factory = TinkoffClientsFactory::create($_ENV['TOKEN']);
        $instruments_request = new InstrumentsRequest();
        $instruments_request->setInstrumentStatus(InstrumentStatus::INSTRUMENT_STATUS_ALL);

        list($response) =  $factory->instrumentsServiceClient->Shares($instruments_request)
            ->wait();

        /** @var SharesResponse[] $instruments_dict */
        $instruments_dict = $response->getInstruments();
        $names = [];
        foreach($instruments_dict as $instrument){
            if($instrument->getCountryOfRisk() === 'RU' && $instrument->getTradingStatus() === 5) {
                $key = $instrument->getTicker();
            $value = $instrument->getFigi();
            $figi[$key] = $value;
            }

        }
        return $figi;
    }

    /**
     * Summary of getIssues
     * @return array
     */
    public static function getIssues() {

        $factory = TinkoffClientsFactory::create($_ENV['TOKEN']);
        $instruments_request = new InstrumentsRequest();
        $instruments_request->setInstrumentStatus(InstrumentStatus::INSTRUMENT_STATUS_ALL);

        list($response) =  $factory->instrumentsServiceClient->Shares($instruments_request)
            ->wait();

        /** @var Share[]  $instruments_dict */
        $instruments_dict = $response->getInstruments();
        $issues = [];
        foreach($instruments_dict as $instrument){
            if($instrument->getCountryOfRisk() === 'RU' && $instrument->getTradingStatus() === 5) {
            $key = $instrument->getFigi();
            $value = $instrument->getIssueSize();
            $issues[$key] = $value;
            }
        }
        return $issues;
    }

    public static function getLots() {

        $factory = TinkoffClientsFactory::create($_ENV['TOKEN']);
        $instruments_request = new InstrumentsRequest();
        $instruments_request->setInstrumentStatus(InstrumentStatus::INSTRUMENT_STATUS_ALL);

        list($response) =  $factory->instrumentsServiceClient->Shares($instruments_request)
            ->wait();

        /** @var Share[]  $instruments_dict */
        $instruments_dict = $response->getInstruments();
        $lots = [];
        foreach($instruments_dict as $instrument){
            if($instrument->getCountryOfRisk() === 'RU' && $instrument->getTradingStatus() === 5) {
            $key = $instrument->getFigi();
                $value = $instrument->getLot();
                $lots[$key] = $value;
                }
            }
        return $lots;
    }
}
