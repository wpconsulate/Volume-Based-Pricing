<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\ShopifyAppAuth;
use App\Models\ShopifyApi;
use App\Models\MerchantConfig;
use Illuminate\Support\Facades\Input;

class authController extends Controller
{
    /**
     * Logic that controls the auth of the user
     * @return on success
     */
    public function authStatus(){
      // This may effect some users who authenticate half way?
      if(!Input::get('shop') || !Input::get('hmac')){
        self::authFail(__FUNCTION__);
      }

      if(Controller::hasSession()){
        self::authSuccess();
      }

      //Retrieve merchant details
      $merchantData = MerchantConfig::where('active', 1)
          ->where('shop_name', Input::get('shop'))
          ->first();

      // No install instance recorded
      if(!$merchantData){
          self::authFail(__FUNCTION__);
      }

      // Failed at some point
      if(!isset($merchantData->access_token) && isset($merchantData) && \Request::path() != 'callback'){
          die(Controller::$message_bag['INSTALLATION_ERROR']);
      }
      elseif(isset($merchantData->access_token) && isset($merchantData)) {
        $shopifyApi = new ShopifyApi; // Verify Hmac is valid - errors handled

        \Session::put('access_token', $merchantData->access_token);

        if(Controller::hasSession()){
          self::authSuccess();
        }
      }

      self::authFail(); // Fallback
    }

    /**
     * If authentication was a success
     * @param  boolean $redirect Decides whether to redirect the user or not
     * @return Redirect
     */
    private static function authSuccess($redirect = true){
      $shopifyAppAuth = new ShopifyAppAuth(new ShopifyApi);
      $shopifyAppAuth->verifyHmac();
      if($redirect){
        \Redirect::to('/index')->send();
      }
    }

    /**
     * If authentication was a failure
     * @param  boolean $redirect Decides whether to redirect the user or not
     * @return Redirect
     */
    private static function authFail($reason = '', $redirect = false){
      if($redirect){
        \Redirect::to('/redirect-to-install')->send();
      }

      $errorMsg = Controller::$message_bag['INSTALLATION_ERROR'];

      if(!env('APP_DEBUG')){
        $reason = '';
      }
      \App::abort(403, $reason);
    }

    /**
     * First Install Pass - This is to check we have all neccasary params in url
     * @param  ShopifyAppAuth $ShopifyAppAuth Instantiate shopifyAppAuth Model
     * @return redirect to installation url
     */
    public function authorise(){
      // Check we have the data we need
      if(!Input::get('hmac') ||
         !Input::get('shop') ||
         !Input::get('timestamp')){
        self::authFail(__FUNCTION__);
      }

      $merchantData = MerchantConfig::where('active', 1)
          ->where('shop_name', Input::get('shop'))
          ->first();

      if(!empty($merchantData) > 0 &&
         $merchantData->install_success = 1 &&
         !is_null($merchantData->install_success)){
        self::authSuccess();
      }

      $shopifyAppAuth = new ShopifyAppAuth;

      return redirect($shopifyAppAuth->authUrl());
    }

    /**
     * Second Install Pass - This is to check we have all neccasary params in url
     * @param  ShopifyAppAuth $ShopifyAppAuth Instantiate shopifyAppAuth Model
     * @return redirect root url, but this time authenticated
     */
    public function callback(ShopifyAppAuth $ShopifyAppAuth){
      // Check we have the data we need
      if(!Input::get('code') ||
         !Input::get('hmac') ||
         !Input::get('shop') ||
         !Input::get('timestamp')){
        self::authFail(__FUNCTION__);
      }

      $merchantData = MerchantConfig::where('active', 1)
          ->where('shop_name', Input::get('shop'))
          ->first();

      if(!$merchantData->install_success){
        $ShopifyAppAuth->oAuth();
      }

      return redirect('/');
    }
}
