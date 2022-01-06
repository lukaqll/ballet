@extends('mail.base')

@section('content')


<table class="btn btn-primary">
    <tbody>
        <tr>
            <td>
                <h1>Olá, {{ $user->name }}</h1>
            </td>
        </tr>
        
        <tr>
            <td>
                <a href="{{ env('APP_URL') }}/password-reset/{{ $token->token }}" target="_blank" class="btn btn-primary text-center">Clique aqui para recuperar sua senha</a>
            </td>
        </tr>
    </tbody>
</table>

@stop