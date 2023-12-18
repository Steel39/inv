<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Trades;
class TradeStream extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:stream';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $get_stream = new Trades;
        $get_stream->connect();
    }
}
