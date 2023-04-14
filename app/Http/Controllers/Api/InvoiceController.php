<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Mail\InvoiceMail;
use App\Models\Parameter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list by user
     * 
     * @return  json
     */
    public function listByUser( $id )
    {
        try {

            $result = $this->invoicesService->list( ['id_user' => $id], ['expires_at'] );
            $response = [ 'status' => 'success', 'data' => InvoiceResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * list by self
     * 
     * @return  json
     */
    public function listSelf()
    {
        try {

            $user = auth('api')->user();
            $result = $this->invoicesService->list( ['id_user' => $user->id], ['expires_at'] )->where('status', '!=', 'C');
            $response = [ 'status' => 'success', 'data' => InvoiceResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * get by key and value
     * 
     * @return  json
     */
    public function get( Request $request ){

        try {

            $dataFilter = $request->all();
            $result = $this->invoicesService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($result) ];
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * get by id
     * 
     * @return  json
     */
    public function getById( $id ){

        try {

            $result = $this->invoicesService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($result) ];

        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * create
     * 
     * @return  json
     */
    public function create( Request $request ){

        try {

            $validData = $request->validate([
                'id_user' => 'required|integer|exists:users,id',
                'expires_at' => 'required|date',
                'value' => 'required|string',
                'send_mail' => 'nullable',
                'fee' => 'required|string',
                'added' => 'required|string',
            ]);

            if( $validData['expires_at'] < date('Y-m-d'))
                throw ValidationException::withMessages(['A data de vencimento não pode ser menor que hoje']);

            $validData['value'] = $this->unMaskMoney($validData['value']);     
            $validData['fee'] = $this->unMaskMoney($validData['fee']);            
            $validData['added'] = $this->unMaskMoney($validData['added']);            

            $validData['expires_at'] = date('Y-m-d 23:59:59', strtotime($validData['expires_at']));
            $validData['status'] = 'A';

            $created = $this->invoicesService->create( $validData );

            if( !empty($validData['send_mail']) ){
                $this->invoicesService->sendInvoiceMail($created);
            }
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($created) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * update
     * 
     * @return  json
     */
    public function update( Request $request, $id ){

        try {
            
            $invoice = $this->invoicesService->find($id);
            if($invoice->status != 'A')
                throw ValidationException::withMessages(['Esta fatura não está mais aberta']);

            $validData = $request->validate([
                'expires_at' => 'required|date',
                'value' => 'required|string',
                'fee' => 'required|string',
                'added' => 'required|string',
                'send_mail' => 'nullable'
            ]);

            $validData['value'] = $this->unMaskMoney($validData['value']);            
            $validData['fee'] = $this->unMaskMoney($validData['fee']);            
            $validData['added'] = $this->unMaskMoney($validData['added']);

            if( 
                date('Y-m-d', strtotime($validData['expires_at'])) != date('Y-m-d', strtotime($invoice->expires_at)) 
                || floatval($validData['value']) != floatval($invoice->value) 
                || floatval($validData['fee']) != floatval($invoice->fee) 
                || floatval($validData['added']) != floatval($invoice->added) 
            ){
                $invoice->update(['reference' => null]);
                if( $invoice->openPayment ){
                    $invoice->openPayment->update(['status' => 'cancelled', 'status_detail' => 'cancelled_update']);
                }
            }
            if( !empty($invoice->reference) ){
                $this->mercadoPagoService->cancelPayment($invoice->reference);
            }

            if( $validData['expires_at'] < date('Y-m-d'))
                throw ValidationException::withMessages(['A data de vencimento não pode ser menor que hoje']);
                
            $validData['expires_at'] = date('Y-m-d 23:59:59', strtotime($validData['expires_at']));

            $updated = $this->invoicesService->updateById( $id, $validData );

            if( !empty($validData['send_mail']) ){
                $this->invoicesService->sendInvoiceMail($invoice);
            }
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * delete
     * 
     * @return  json
     */
    public function cancelInvoice( $id ){

        try {

            $cancelled = $this->invoicesService->cancelInvoice($id);
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($cancelled) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }


    public function getInvoicePayment( $id ){

        try {


            $invoiceAllow = Parameter::where('operation', 'general-config')
                                    ->where('attribute', 'invoice_allow')
                                    ->where('value', '1')
                                    ->first();
            if(empty($invoiceAllow))
                throw new Exception('Estamos em manutenção, tente mais tarde :)');

            $invoice = $this->invoicesService->find($id);
            
            if($invoice->status != 'A')
                throw new Exception('Esta fatura não está mais aberta');
    
            $invoicePayment = $invoice->openPayment;
    
            if( !empty($invoicePayment) ){
    
                $payment = $this->mercadoPagoService->getPayment( $invoicePayment->reference );
    
                // verify expiration
                if( strtotime($payment->date_of_expiration) < strtotime(date('Y-m-d H:i:s')) ){
    
                    // create a new payment
                    $invoice->openPayment->update(['status' => 'cancelled', 'status_detail' => 'cancelled_expired']);
                    $payment = $this->mercadoPagoService->createTicket( $invoice );
    
                    // update invoice reference
                    $invoice->update(['reference' => $payment->id]);
                }
    
                
            } else {
                $payment = $this->mercadoPagoService->createTicket( $invoice );
            }
            
            // show PDF
            return redirect($payment->transaction_details->external_resource_url);
        } catch (Exception $e){
            
            return view('errors.general-error', [
                'backTo' => '/faturas',
                'title'  => 'Ops... Houve um erro',
                'message'=> $e->getMessage()
            ]);
        }
    }


    /**
     * payManually
     */
    public function payManually(Request $request, $id){

        try {

            $invoice = $this->invoicesService->find($id);
            if($invoice->status != 'A')
                throw ValidationException::withMessages(['Esta fatura não está mais aberta']);

            if( !empty($invoice->reference) ){
                $this->mercadoPagoService->cancelPayment($invoice->reference);
            }

            if( !empty($invoice->openPayment) ){
                $invoice->openPayment->update(['status' => 'payd', 'status_detail' => 'payd_manual']);
            }

            $validData = $request->validate([
                'paid_at' => 'nullable|date',
                'method' => 'nullable|integer|exists:payment_methods,id',
                'receipt' => 'nullable|mimes:jpg,jpeg,png,bmp,gif,pdf'
            ]);

            if (!empty($validData['receipt'])) {
                $invoice = $this->invoicesService->find($id);
                $this->invoicesService->uploadReceipt($invoice, $validData['receipt']);
                unset($validData['receipt']);
            }

            if (empty($validData['paid_at'])) {
                $validData['paid_at'] = date('Y-m-d');
            }

            $validData['status'] = 'P';
            $validData['manual'] = 1;
            $validData['closed_at'] = date('Y-m-d H:i:s');

            $updated = $this->invoicesService->updateById($id, $validData);
            $this->usersService->verifyUserStatus( $invoice->user );
            
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function sendInvoiceMail( $id ){
        try {

            $invoice = $this->invoicesService->find($id);
            if($invoice->status != 'A')
                throw ValidationException::withMessages(['Esta fatura não está mais aberta']);

            $this->invoicesService->sendInvoiceMail($invoice);

            $response = [ 'status' => 'success', 'data' => true ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function sendAllUsersInvoicesMail(Request $request) {
        try {
            $result = $this->invoicesService->sendAllUsersInvoicesMail();
            $response = [ 'status' => 'success', 'data' => $result ];
        } catch (ValidationException $e) {
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function attachReceipt(Request $request, $id) {
        try {
            $invoice = $this->invoicesService->find($id);
            if(empty($invoice))
                throw ValidationException::withMessages(['Não é possível anexar um comprovante à esta fatura']);

            $validData = $request->validate([
                'receipt' => 'required|mimes:jpg,jpeg,png,bmp,gif,pdf'
            ]);

            if (empty($this->invoicesService->uploadReceipt($invoice, $validData['receipt'])))
                throw ValidationException::withMessages(['Houve um erro ao carregar o arquivo']);

            $response = [ 'status' => 'success', 'data' => true ];
        } catch (ValidationException $e) {
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function createForUnsignedUsers() {
        try {
            DB::beginTransaction();
            $result = $this->invoicesService->generateInvoicesForUnsigned();
            $response = [ 'status' => 'success', 'data' => $result ];
            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function getUnsignedUsers() {
        try {
            $result = $this->invoicesService->getUnsignedUsersToInvoice();
            $response = [ 'status' => 'success', 'data' => count($result) ];
        } catch (ValidationException $e) {
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}