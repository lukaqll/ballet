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
            having count(id) > 1
            order by count(id) desc
        ");
    }

    public function getRevenueByMonth(){

        $result = DB::select("
            select 
                date_format(paid_at, '%Y-%m') as month, sum(value+fee) as value
            from invoices
            where status = 'P'
            group by month;
        ");

        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Des'];
        foreach( $result as &$item ){

            $monthText = $months[ intval(date('m', strtotime($item->month)))+1 ];
            $dateText = $monthText . ' de ' . date('Y', strtotime($item->month));
            $item->month = $dateText;
        }

        return $result;
    }
}