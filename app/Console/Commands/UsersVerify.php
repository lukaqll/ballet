<?php

namespace App\Console\Commands;

use App\Services\UsersService;
use Illuminate\Console\Command;

class UsersVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify';

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
        
        $usersService = new UsersService;
        $users = $usersService->listClients();

        foreach($users as $user){

            if( in_array($user->status, ['CP', 'MP', 'I']) )
                continue;

            echo "\n User: $user->name";
            $usersService->verifyUserStatus($user);
        }
    }
}
