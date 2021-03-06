<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }
    /**
     * Redirect the user to the Stripe authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('stripe')->redirect();
    }

    /**
     * Obtain the user information from Stripe.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $socialite = Socialite::driver('stripe')->user();
        $user = \App\User::firstOrNew([
          'name' => $socialite->user['business_name'],
          'email'=> $socialite->getEmail()
        ]);

        $user->fill([
          'secret_key' => $socialite->token,
          'tag' => snake_case($socialite->user['business_name']),
          'stripe_account_id' => $socialite->user['id']
        ]);
        $user->save();
        auth()->login($user);
        return redirect('/home');

        // $socialite->token;
    }
}
