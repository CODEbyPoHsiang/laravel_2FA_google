@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">設置Google 二次驗證</div>

                <div class="panel-body" style="text-align: center;">
                    {{-- <p>Set up you 2FA by scanning the barcode below. Alternatively, you can use the code {{ $secret }}</p> --}}
                    <p>通過掃描QR code 來設置 Google 2次驗證。或者您可以使用代碼 {{ $secret }}</p>
                    <div>
                        <img src="{{ $QR_Image }}">
                    </div>
                    @if (!@$reauthenticating)
                        <p>You must set up your Google Authenticator app before continuing. You will be unable to login otherwise</p>
                        <p>您必須先設置Google 二次驗證，然後才能繼續。否則您將無法登錄</p>
                        <div>
                            <a href="/complete-registration"><button class="btn-primary">完成註冊</button></a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
