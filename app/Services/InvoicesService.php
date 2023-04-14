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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

        $result = collect(DB::select(
            "SELECT 
                us.id
            FROM users us
            JOIN students st ON st.id_user = us.id
                AND st.status = 'A'
            JOIN student_classes sc ON sc.id_student = st.id
                AND sc.approved_at IS NOT NULL
            JOIN classes cl ON cl.id = sc.id_class
            WHERE us.status IN ('A', 'P')
            GROUP BY us.id;
            "
        ));

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

                $value = $this->getInvoiceValue($user);
                $invoice = $this->generateInvoiceToUser( $user, $value );
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
     * get users to generate invoice
     * 
     * @return array
     */
    public function getUnsignedUsersToInvoice(){

        return collect(DB::select(
            "SELECT 
                us.id
            FROM users us
            JOIN students st ON st.id_user = us.id
                AND st.status = 'CP'
            JOIN contracts ct on ct.id_student = st.id
                and ct.status = 'running'
            JOIN student_classes sc ON sc.id_student = st.id
            JOIN classes cl ON cl.id = sc.id_class
            WHERE us.status IN ('A', 'P')
            GROUP BY us.id;
            "
        ));
    }

    /**
     * generate to unsigned users
     * run on script
     * 
     * @return array
     */
    public function generateInvoicesForUnsigned(){

        $userModel = new User;
        $usersResult = $this->getUnsignedUsersToInvoice();

        $generated = [];
        foreach( $usersResult as $item ){
            
            $user = $userModel->find($item->id);
            $result = ['name' => $user->name];
            
            try {
                $value = $this->getUnsignedUserInvoiceValue($user);
                $invoice = $this->generateInvoiceToUser( $user, $value );
                if ( $invoice ) {
                    $result['message'] = "Fatura gerada | R$ {$invoice->value}";
                } else {
                    $result['message'] = "Fatura não gerada";
                }
            } catch ( Exception $e ){
                $result['message'] = $e->getMessage();
            }
            $generated[] = $result;
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
    public function getInvoiceValue( User $user ){

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
     * get invoice value
     * @param User
     * @return float
     */
    public function getUnsignedUserInvoiceValue( User $user ){
        
        $value = 0;

        foreach($user->students as $student){
            if ($student->status != 'CP') continue;
            
            foreach($student->studentClasses as $studentClass){
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
    public function generateInvoiceToUser( User $user, $invoiceValue ){

        // first day of month
        $now = date('Y-m-01');
        $curMonth = date('Y-m', strtotime($now));

        $day = $user->unit->due_day;
        if ($day < 1)
            $day = 1;

        if ($day > date('t'))
            $day = date('t');
        
        // expires at day 10
        $expiration = date("Y-m-{$day} 23:59:59", strtotime($now));

        // get invoice from current month
        $isInvoiceMonthExists = $this->model->where('status', '!=', 'C')
                                            ->where('id_user', $user->id)
                                            ->whereRaw("date_format(created_at, '%Y-%m') = '$curMonth'")
                                            ->first();

        if( !empty($isInvoiceMonthExists) ) {
            $month = implode('/', array_reverse(explode('-', $curMonth)));
            throw new Exception("Fatura do mês {$month} já gerada");
        }
        
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
        $ticket->invoice->update(['status' => 'P', 'paid_at' => date('Y-m-d H:i:s'), 'closed_at' => date('Y-m-d H:i:s')]);

        // verify store status
        $user = $ticket->invoice->user;
        $userService->verifyUserStatus( $user );
    }

    public function sendInvoiceMail(Invoice $invoice){
        if (env('APP_ENV') != 'prod') return;

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

    public function sendAllUsersInvoicesMail() {
        $invoices = Invoice::join('users', function($join) {
                                $join->on('users.id', 'invoices.id_user')
                                ->where('users.status', 'A');
                            })
                            ->where('invoices.status', 'A')
                            ->select('invoices.*')
                            ->get();

        foreach ($invoices as $invoice) {
            $this->sendInvoiceMail($invoice);
        }
        
        return true;
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

    public function uploadReceipt(Invoice $invoice, $file) {
        if( empty($file) )
            return false;
        
        // upload
        $fileName = Storage::disk('public')->put('/receipts', $file);

        if (!empty($invoice->receipt)) {
            Storage::disk('public')->delete($invoice->receipt);
        }
        
        if( $fileName ){
            $invoice->update(['receipt' => $fileName]);
        }

        return $invoice;
    }

    public function cancelInvoice($id) {

        $invoice = $this->find($id);
        if($invoice->status != 'A')
            throw ValidationException::withMessages(['Esta fatura não está mais aberta']);

        $mercadoPagoService = new MercadoPagoService;
        if( !empty($invoice->reference) ){
            $mercadoPagoService->cancelPayment($invoice->reference);
        }

        if( !empty($invoice->openPayment) ){
            $invoice->openPayment->update(['status' => 'cancelled', 'status_detail' => 'cancelled_manual']);
        }

        return $this->updateById( $id, ['status' => 'C'] );
    }
}