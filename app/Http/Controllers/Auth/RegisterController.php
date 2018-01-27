<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Role;
use GuzzleHttp\Client;

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

    use RegistersUsers;

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

        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user
            ->roles()
            ->attach(Role::where('name', 'employee')->first());

        return $user;
    }

    /*
     * Resell Biz API call to create a Domain User
     * @param array $data
     * @return
     *
     */
    protected function domainUserUpdate(array $data) {

        $api_url = env('API_URL');

        $data['api-key'] = env('API_KEY');
        $data['auth-userid'] = env('AUTH_USERID');
        $data['passwd'] = $data['password'];
        $data['username'] = $data['email'];


        $client = new Client(['base_uri' => $api_url]);

        $response = $client->request('POST', 'api/customers/signup.xml', [
            'form_params' => $data
        ]);

        $body = $response->getBody();

    }
}
