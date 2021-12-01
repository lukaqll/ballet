<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ApiControllerMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:apiController {name} {--s=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $name = $this->argument('name');

        // get service name
        $servicelOpt = $this->option('s');
        if( empty($servicelOpt)){
            $serviceName = $this->ask('What is the service?');
        } else {
            $serviceName = $servicelOpt[0];
        }

        $upperServiceName = ucfirst($serviceName);
        $lowerServiceName = lcfirst($serviceName);

        $controllerBladeTemplate = view('templates/commands/apiControllerTemplate', [
            'name' => $name,
            'upperServiceName' => $upperServiceName,
            'lowerServiceName' => $lowerServiceName
        ]);

        $controllerTemplate = "<?php \n\n {$controllerBladeTemplate}";

        Storage::disk('apiController')->put($name.'.php', $controllerTemplate);
        return $this->info('ApiController created successfully.');
    }
}