<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\ShopifyAppAuth;
use App\Models\MerchantConfig;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * Used for general messages that need to be printed to the screen
     * @var Array
     */
    public static $message_bag = [
      'INSTALLATION_ERROR' => 'There has been an error in the installation, please uninstall and try again'
    ];

    /**
     * Connect This is to ensure that the connection between this app and shopify is correct
     * @param  authController $auth
     * @return view
     */
    public function connect(authController $auth){
      // $auth->authStatus();
      $merchantData = MerchantConfig::where('active', 1)
          ->where('access_token', \Session::get('access_token'))
          ->firstOrFail();

      return view('home', [
        'apiKey' => env('SHOPIFY_API_KEY'),
        'merchantData' => $merchantData
      ]);
    }

    /**
     * Check if the current user already has a session
     * @return boolean
     */
    public static function hasSession(){
      return \Session::has('access_token');
    }

    /**
     * Redirect within an iFrame - specific for shopify
     * @return view
     */
    public static function redirectIframe(){
      return view('redirect', ['url' => env('SHOPIFY_API_APP_STORE')]);
    }
}
