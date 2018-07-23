<?php

namespace App\Console\Commands;

use App\Services\KibanaService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetTestOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kibana:get {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kibana';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        if (!($from = $this->option('from'))) {
            $from = Carbon::now()->timezone('Australia/Sydney')->startOfDay()->subDay();
        }else{
            $from = Carbon::parse( $this->option('from'),new \DateTimeZone('Australia/Sydney'))->startOfDay();
        }


        if (!($to = $this->option('to'))) {
            $to = Carbon::now()->timezone('Australia/Sydney')->endOfDay()->subDay();
        }else{
            $to = Carbon::parse( $this->option('to'),new \DateTimeZone('Australia/Sydney'))->endOfDay();
        }
        $service = new KibanaService();
        $service->syncTestData($from,$to);
    }
}
