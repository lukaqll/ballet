<?php 
 namespace App\Services;

use App\Jobs\InvoiceMail as JobsInvoiceMail;
use App\Mail\InvoiceMail;
use App\Models\ClassModel;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Parameter;
use App\Models\StudentClass;
use App\Models\User;
use App\Services\Api\MercadoPagoService;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

    public function getExpiredInvoices(){

        return $this->model->where('status', 'A')
                           ->whereRaw('expires_at < curdate()')
                           ->get();
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
                if( $invoice ){
                    echo "\n -> fatura gerada | R$ {$invoice->value}";
                } else {
                    echo "\n -> fatura não gerada";
                }

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
                $value += $class->value;
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
        $expiration = date('Y-m-d 23:59:59', strtotime('+7 days', strtotime($now)));

        // get invoice from current month
        $isInvoiceMonthExists = $this->model->where('status', '!=', 'C')
                                            ->where('id_user', $user->id)
                                            ->whereRaw("date_format(created_at, '%Y-%m') = '$curMonth'")
                                            ->first();

        if( !empty($isInvoiceMonthExists) )
            throw new Exception("Fatura do mês {$curMonth} já gerada");

        // calc proportional
        $invoiceValue = $this->getInvoiceValue($user, $now);
        
        if( $invoiceValue < 3.49 ){
            return false;
        }

        // get invoice adds for this month
        $toAdds = $user->invoiceAdds()
                      ->where('month', $now)
                      ->where('id_invoice', null)
                      ->get();
        $toAddValue = 0;
        foreach($toAdds as $toAdd){
            $toAddValue += floatval($toAdd->value);
        }

        // create invoice
        $invoiceData = [
            'id_user'    => $user->id,
            'value'      => $invoiceValue,
            'status'     => 'A',
            'reference'  => '',
            'expires_at' => $expiration,
            'added'      => $toAddValue
        ];

        $invoice = $this->create($invoiceData);

        foreach($toAdds as $toAdd){
            $toAdd->update(['id_invoice' => $invoice->id]);
        }
            
        $this->sendInvoiceMail($invoice);

        return $invoice;
    }

    public function generateClassInvoice( StudentClass $studentClass ){

        $student = $studentClass->student;
        $user = $student->user;

        $now = date('Y-m-d');
        $nextInvoiceDate = date('Y-m-01', strtotime('+1 month', strtotime($now)));

        // expires at day 3
        $expiration = date('Y-m-d 23:59:59', strtotime('+2 days', strtotime($now)));

        $invoiceValue = $this->proportionalCalc( $now, $nextInvoiceDate, $studentClass->class );

        // create invoice
        $invoiceData = [
            'id_user'    => $user->id,
            'value'      => $invoiceValue,
            'status'     => 'A',
            'reference'  => '',
            'expires_at' => $expiration
        ];

        if( floatval($invoiceValue) > 3.49 ){

            $invoice = $this->create($invoiceData);
            // $this->sendInvoiceMail($invoice);
    
            return $invoice;
        } else {
            return false;
        }
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
    public function proportionalCalc( string $startDate, string $endDate, ClassModel $class ){

        $monthStart = date('Y-m-08', strtotime($startDate));
        $monthEnd = date('Y-m-07', strtotime('+1 month', strtotime($monthStart)));

        // amount of classes by month
        $amounInMonth = $this->getAmountOfClassByPeriod( $monthStart, $monthEnd, $class );

        // amount of classes by period
        $amountInPeriod = $this->getAmountOfClassByPeriod( $startDate, $endDate, $class );

        // value of each class
        $valuePerClass = floatval($class->value) / $amounInMonth;

        // total value by period
        $total = $valuePerClass * ($amountInPeriod);

        // echo "\n $startDate a $endDate";
        // echo "\n $amounInMonth aulas neste mês";
        // echo "\n $amountInPeriod aulas neste período";
        // echo "\n $valuePerClass reais por aula";

        return round($total, 2);
    }


    public function getAmountOfClassByPeriod( string $startDate, string $endDate, ClassModel $class ){

        $dates = [
            [
                'date' => $startDate,
                'weekday' => intval(date('N', strtotime($startDate)))-1
            ]
        ];

        // days in interval
        while( end($dates)['date'] < $endDate ){
            $dt = date('Y-m-d', strtotime('+1 day', strtotime(end($dates)['date'])));
            $dates[] = [
                'date' => $dt,
                'weekday' => intval(date('N', strtotime($dt)))-1
            ];
        }

        // amount of classes in period
        $classQtd = 0;
        foreach( $class->times as $time ){

            foreach( $dates as $date ){

                if( $date['weekday'] == $time->weekday )
                    $classQtd++;
            }
        }

        return $classQtd;
    }

        /**
     * when a payment was executed
     */
    public function onInvoicePayHandle( InvoicePayment $ticket ){

        $userService = new UsersService;

        // update invoice status
        $ticket->invoice->update(['status' => 'P', 'paid_at' => date('Y-m-d H:i:s')]);

        // verify store status
        $user = $ticket->invoice->user;
        $userService->verifyUserStatus( $user );
    }

    public function sendInvoiceMail(Invoice $invoice){

        try {
            
            $emailAllowParam = Parameter::where('operation', 'general-config')
                                        ->where('attribute', 'send_invoice_mail')
                                        ->where('value', '1')
                                        ->first();

            if(!empty($emailAllowParam)){
                Mail::send(new InvoiceMail($invoice));
            }

        } catch (Exception $e){

        }

    }

    public function feeVerify( Invoice $invoice ){

        if( !$invoice->getIsExpired() )
            return false;

        $mercadoPagoService = new MercadoPagoService;

        $expirationDate = new DateTime( $invoice->expires_at );
        $now = new DateTime();
        $dateInterval = $expirationDate->diff( $now );
        $amountOfDays = intval($dateInterval->days);

        $invoiceValue = floatval($invoice->value);
        
        $multa = ($invoiceValue * 2) / 100;
        $juros = ($invoiceValue * (0.033*$amountOfDays)) / 100;
        $totalFee = round(($multa + $juros), 2);

        // echo "\n dias = $amountOfDays, multa = $multa, juros = $juros, total => $totalFee \n";

        $invoice->update(['fee' => $totalFee, 'reference' => null]);

        if( !empty($invoice->reference) ){
            $mercadoPagoService->cancelPayment($invoice->reference);
        }

        return $invoice;
    }
}