<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\MerchantConfig;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public static $message_bag = [
      'INSTALLATION_ERROR' => 'There has been an error in the installation, please uninstall and try again'
    ];

    public function connect(authController $auth){
      $auth->authStatus();

      $merchantData = MerchantConfig::where('active', 1)
          ->where('access_token', \Session::get('access_token'))
          ->firstOrFail();

      return view('welcome', [
        'apiKey' => env('SHOPIFY_API_KEY'),
        'merchantData' => $merchantData
      ]);
    }

    public static function hasSession(){
      return \Session::has('access_token');
    }
    public static function redirectIframe(){
      return view('redirect', ['url' => env('SHOPIFY_API_APP_STORE')]);
    }
}
