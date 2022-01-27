<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MercadoPagoHookController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @return  json
     */
    public function hooksCallback( Request $request )
    {
        try {

            DB::beginTransaction();

            $out = [];
            $type = $request->get('type');
            $data = $request->get('data');

            $ticket = null;
            switch( $type ){

                // payment notification
                case 'payment':

                    $payment = $this->mercadoPagoService->getPayment( $data['id'] );

                    // ---------------- PAID TEST ONLY !!!!
                    // $payment->status = 'approved';
                    // $payment->status_detail = 'accredited';
                    // ---------------- END PAID TEST ONLY !!!!


                    if( !empty($payment->external_reference) ){
                        

                        // update ticket status
                        $ticket = $this->invoicePaymentsService->get([
                            'id_invoice' => $payment->external_reference, 
                            'status' => 'pending'
                        ]);
                        if( !empty( $ticket )  ){
                            $ticket->update([
                                'status'        => $payment->status,
                                'status_detail' => $payment->status_detail
                            ]);
                        }
                        
                        // is ticket exists
                        if( !empty( $ticket ) ){

                            // payd
                            if( $payment->status == 'approved' ){
                                $this->invoicesService->onInvoicePayHandle( $ticket );
                            }
                        }
                    }

                    $out = ['status' => true, 'data' => $ticket];
                break;
            }
            DB::commit();
        } catch ( Exception $e ){
            DB::rollBack();
            $out = ['status' => false, 'message' => $e->getMessage()];
        }

        return response()->json($out);
    }

}