<?php
namespace App\Services\Api;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Exception;
use Illuminate\Validation\ValidationException;
use MercadoPago;

class MercadoPagoService
{
    protected $payment;

    public function __construct()
    {
        MercadoPago\SDK::setAccessToken( env('MP_ACCESS_TOKEN') );
        $this->payment = new MercadoPago\Payment();
    }


    /**
     * create invoice payment
     * 
     * @param Invoice $invoice
     * 
     * @return object $payment
     */
    public function createTicket( Invoice $invoice ){

        $user = $invoice->user;

        // date of expiration
        if( $invoice->expires_at > date('Y-m-d 23:59:59') ){
            $expiresDateTime = strtotime($invoice->expires_at);
        } else {
            // expired
            $expiresDateTime = strtotime('+2 days', strtotime(date('Y-m-d 23:59:59')));
        } 

        $months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $expirantionText = date('d') . ' de ' . $months[intval( date('m') )-1];

        $expirationDate = date( 'Y-m-d', $expiresDateTime) . "T" . date( 'H:i:s.300', $expiresDateTime) . 'Z';

        // doc from user
        $docNumber = str_replace('.', '', $user->cpf);
        $docNumber = str_replace('-', '', $docNumber);
        $docNumber = str_replace('/', '', $docNumber);
        $docNumber = str_replace(' ', '', $docNumber);

        // payment data
        $this->payment->transaction_amount = floatval($invoice->value) + floatval($invoice->fee);
        $this->payment->description = "Fatura Ellegance Ballet $expirantionText";
        $this->payment->payment_method_id = "bolbradesco";
        $this->payment->external_reference = $invoice->id;
        $this->payment->date_of_expiration = $expirationDate;

        $nameParts = explode(' ', $user->name);
        $firstName = $nameParts[0];
        $lastname = count($nameParts) > 1 ? end($nameParts) : ' ';

        // payer data
        $this->payment->payer = [
            'email'      => $user->email,
            'first_name' => $firstName,
            'last_name'  => $lastname,
            // 'entity_type'    => 'individual',
            // 'type'           => 'customer',

            'identification' => [
                'type'   => 'CPF',
                'number' => $docNumber
            ],
            'address'=>  [
                'zip_code' => str_replace('-', '', $user->cep),
                'street_name' => $user->street,
                'street_number' => $user->address_number,
                'neighborhood' => $user->district,
                'city' => $user->city,
                'federal_unit' => $user->uf
            ]
        ];

        // dd($this->payment);
        // throw new Exception($expirationDate . "\n\n\n");

        // save payment
        if( $this->payment->save() ){

            // create invoice payment
            $invoicePaymentModel = new InvoicePayment;
            $invoicePayment = $invoicePaymentModel->create([
                'id_invoice' => $invoice->id,
                'type' => $this->payment->payment_method_id,
                'value' => $this->payment->transaction_amount,
                'reference' => $this->payment->id,
                'status' => $this->payment->status,
                'status_detail' => $this->payment->status_detail
            ]);

            // update ivoice reference as payment id
            $invoice->update(['reference' => $this->payment->id]);
        } else {
            throw new Exception('Falha ao gerar pagamento');
        }
        
        return $this->payment;
    }

    /**
     * get payment by id
     */
    public function getPayment( int $id ){

        $payment = $this->payment->find_by_id( $id );
        return $payment;
    }

    /**
     * create cancel
     */
    public function cancelPayment( int $idPayment ){

        $payment = $this->getPayment($idPayment);
        $payment->status = "cancelled";
        $payment->update();
        return $payment;
    }

}