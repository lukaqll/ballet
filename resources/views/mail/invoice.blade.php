@extends('mail.base')

@section('content')


<table class="btn btn-primary">
    <tbody>
        <tr>
            <td>
                <h1>Olá, {{ $user->name }}</h1>
                <h3>Sua Fatura Ellegance Ballet já está disponível</h3>
                <p>
                    Valor: R$ {{ number_format($invoice->value, 2, ',', ',') }}
                    <br>
                    Vencimento: {{ date('d/m/Y', strtotime($invoice->expires_at)) }}
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <a href="{{ env('APP_URL') }}/invoice-payment/get/{{ $invoice->id }}" target="_blank" class="btn btn-primary text-center">Clique aqui para acessar o boleto</a>
            </td>
        </tr>
    </tbody>
</table>

@stop