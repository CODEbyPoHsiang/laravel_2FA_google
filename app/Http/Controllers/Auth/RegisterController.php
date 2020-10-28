<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
        register as registration;
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
         User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'google2fa_secret' => $data['google2fa_secret'],
        ]);
    }

    public function register(Request $request)
    {
        // Validate the incoming request 驗證傳入的請求
        $this->validator($request->all())->validate();

        // initialise the 2FA class 初始化 2FA
        $google2fa = app('pragmarx.google2fa');

        // save the registration data in an array 將註冊的data保存到陣列中
        $registration_data = $request->all();
        // dd($registration_data);

        // add the secret key to the registration data 將秘鑰存到註冊時的data內
        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();

        // dd($registration_data);

        // save the registration data to the user session for just the next request
        // 將註冊data保存到session，僅用於下一個請求
        $request->session()->flash('registration_data', $registration_data);

        // generate the QR image 
        //保存 QR圖像
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $registration_data['email'],
            // $registration_data['password'],
            // $registration_data['name'],
            $registration_data['google2fa_secret']
        );
        // dd($QR_Image);


        // Pass the QR barcode image to our view.
        //將圖片傳送到view
        return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
    //     $response = ['QR_Image' => $QR_Image, 'data' => $registration_data];
    //     dd($response);
    //     return response()->json($response);
    }

    public function completeRegistration(Request $request)
    {        
        // add the session data back to the request input
        $opt = $request->merge(session('registration_data'));


        // Call the default laravel authentication
        return $this->registration($request);
    }
}
