<?php

namespace App\Support;

use PragmaRX\Google2FALaravel\Support\Authenticator;
use Carbon\Carbon;
use Google2FA;
use App\User;

use Illuminate\Http\Request as IlluminateRequest;
use PragmaRX\Google2FA\Support\Constants as Google2FAConstants;
use PragmaRX\Google2FALaravel\Exceptions\InvalidOneTimePassword;
use PragmaRX\Google2FALaravel\Exceptions\InvalidSecretKey;

class Google2FAAuthenticator extends Authenticator
{
    // protected function getUser()
    // {
    //     $user =  $this->getAuth()->user();
    //     // dd($user);
    // }

    protected function canPassWithoutCheckingOTP()
    {
         $user =$this->getUser()->get()->toArray();
        //這裡可以獲得登入時點擊後的資料
        // dd($user);

             if(!count(array($this->getUser()->passwordSecurity)))
                 return true;
             return
            // !$this->getUser()->passwordSecurity->google2fa_enable ||
            // !$this->isEnabled() ||
            $this->noUserIsAuthenticated()|| $this->twoFactorAuthStillValid();
        //  }
    }

    protected function getGoogle2FASecretKey()
    {
       

        $secret = $this->getUser()->{$this->config('otp_secret_column')};

        // dd($secret);


        if (is_null($secret) || empty($secret)) {
            throw new InvalidSecretKey('Secret key cannot be empty.');
        }

        return $secret;
    }

}