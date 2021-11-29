<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ServiceMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--m=*} {--c}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a service file';

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
        
        // get model name
        $modelOpt = $this->option('m');
        if( empty($modelOpt) || empty($modelOpt[0])){
            $model = $this->ask('What is the model?');
        } else {
            $model = $modelOpt[0];
        }

        $serviceName = $this->argument('name');

        // create model
        $this->call("make:model", ['name' => $model]);

        // get service template
        $serviceBladeTemplate = view('templates/commands/serviceTemplate', [
            'name'  => $serviceName,
            'model' => $model
        ]);
        $serviceTemplate = "<?php \n $serviceBladeTemplate";

        if( $this->option('c') ){

            $controllerName = 'Api'. ucfirst($model) . 'Controller';
            $this->call("make:apiController", [
                'name' => $controllerName, 
                '--s' => [$serviceName]
            ]);
        }

        // create file
        Storage::disk('services')->put( $serviceName.'.php' , $serviceTemplate);

        return $this->info('Service created successfully.');
    }
}
