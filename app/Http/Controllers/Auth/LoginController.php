<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
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
    // 重写 AuthenticatesUsers 中的 login 方法
    public function login(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

           /* $http = new GuzzleHttp\Client;

            $response = $http->post('http://blog.test/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => 'your-refresh-token',
                    'client_id' => 'your-client-id',
                    'client_secret' => 'your-client-secret',
                    'scope' => '*',
                ],
            ]);

            return response($response->getBody());*/

            $http = new Client();
            // 发送相关字段到后端应用获取授权令牌
            $response = $http->post(config('services.auth.redirect'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.auth.appid'),
                    'client_secret' => config('services.auth.secret'),
                    'username' => $request->input('email'),  // 这里传递的是邮箱
                    'password' => $request->input('password'), // 传递密码信息
                    'scope' => '*'
                ],
            ]);

            return response($response->getBody());
        }catch (\Exception $e){
            \Log::error($e->getMessage());
        }

    }
    public function oauth()
    {

        $query = http_build_query([
            'client_id' => config('services.auth.appid'),
            'redirect_uri' => config('services.auth.callback'),
            'response_type' => 'code',
            'scope' => '',
        ]);

        return redirect('http://oauth.renzhifan.cn/oauth/authorize?'.$query);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            dd('授权失败');
        }
        $http = new Client();
        $response = $http->post(config('services.auth.redirect'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.auth.appid'),  // your client id
                'client_secret' => config('services.auth.secret'),   // your client secret
                'redirect_uri' => config('services.auth.callback'),
                'code' => $code,
            ],
        ]);

        return response($response->getBody());
    }
    public function client()
    {
        $http = new Client();
        $response = $http->post('http://oauth.renzhifan.cn/oauth/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.auth.appid'),  // your client id
                'client_secret' => config('services.auth.secret'),   // your client secret
                'scope' => '*'
            ],
        ]);

        return response($response->getBody());
    }


}
