<?php 
 namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ReportsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Invoice;       
    }

    public function getKnowBy(){

        return DB::select("
            select 
                trim(know_by) as name, count(id) as count from users
                where know_by is not null
            group by trim(know_by)
            order by count(id) desc
        ");
    }
}