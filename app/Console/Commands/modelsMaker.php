<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModelsMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:models {--m=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create muiltiple models at once';

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
     * @return int
     */
    public function handle()
    {
        $modelsStr = $this->option('m');

        $modelsArray = explode(',', $modelsStr);

        foreach( $modelsArray as $model ){
            $model = trim($model);
            $this->call("make:model", ['name' => $model]);
        }

        return Command::SUCCESS;
    }
}
