<?php 
 namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class InvoicesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Invoice;       
    }

    /**
     * get users to generate invoice
     * 
     * @return array
     */
    public function getUsersToInvoice(){

        $result = collect(DB::select("
            select 
                us.id
            from users us
            join students st
                on st.id_user = us.id
                and st.status = 'A'
            join student_classes sc
                on sc.id_student = st.id
                and sc.approved_at is not null
            join classes cl
                on cl.id = sc.id_class
            where 
                us.status IN ('A', 'P')
            group by us.id;"));

        return $result;
    }

    /**
     * generate to all users
     * run on script
     * 
     * @return array
     */
    public function generateInvoices(){

        $userModel = new User;
        $usersResult = $this->getUsersToInvoice();

        $generated = [];
        foreach( $usersResult as $item ){

            $user = $userModel->find($item->id);

            echo "\n {$user->name}";
            try {

                $invoice = $this->generateInvoiceToUser( $user );
                echo "\n -> fatura gerada | R$ {$invoice->value}";

            } catch ( Exception $e ){
                echo "\n -> {$e->getMessage()}";
            }
            echo "\n";
        }

        return $generated;
    }

    /**
     * get invoice value
     * 
     * @param User
     * 
     * @return float
     */
    public function getInvoiceValue( User $user, string $now ){

        $students = $user->students;
        $value = 0;

        // each student
        foreach($students as $student){

            // is active or pending
            if( !in_array($student->status, ['A', 'P']) )
                continue;

            // approved classes
            $studentClasses = $student->approvedStudentClasses;
            
            // each approved class
            foreach($studentClasses as $studentClass){

                // get class
                $class = $studentClass->class;
                $value += $this->proportionalCalc( $studentClass->approved_at, $now, $class->value );
            }
        }
        
        return $value;
    }

    /**
     * generate invoice
     * 
     * @param User $user
     * @param float $value
     * 
     * @return Invoice
     */
    public function generateInvoiceToUser( User $user ){

        // first day of month
        $now = date('Y-m-01');
        $curMonth = date('Y-m', strtotime($now));
        
        // expires at day 10
        $expiration = date('Y-m-d 23:59:59', strtotime('+9 days', strtotime($now)));

        // get invoice from current month
        $isInvoiceMonthExists = $this->model->where('status', '!=', 'C')
                                            ->where('id_user', $user->id)
                                            ->whereRaw("date_format(created_at, '%Y-%m') = '$curMonth'")
                                            ->first();

        if( !empty($isInvoiceMonthExists) )
            throw new Exception("Fatura do mês {$curMonth} já gerada");

        // calc proportional
        $invoiceValue = $this->getInvoiceValue($user, $now);
        
        // create invoice
        $invoiceData = [
            'id_user'    => $user->id,
            'value'      => $invoiceValue,
            'status'     => 'A',
            'reference'  => '',
            'expires_at' => $expiration
        ];
        $invoice = $this->create($invoiceData);

        return $invoice;
    }

    /**
     * calc proportional
     * 
     * @param string $startDate
     * @param string $endDate
     * @param float $value
     * 
     * @return float
     */
    public function proportionalCalc( string $startDate, string $endDate, float $value ){

        $startDatetime = date_create( $startDate );
        $endDatetime   = date_create( $endDate );

        $interval = date_diff($startDatetime, $endDatetime);

        $valuePerDay = $value / 30;

        $newValue = $valuePerDay * $interval->format('%a');
        if( $newValue > $value )
            return $value;

        return round($newValue, 2);
    }
}