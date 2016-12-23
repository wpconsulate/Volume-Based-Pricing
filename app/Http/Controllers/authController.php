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
    public function __construct(){
      self::authStatus();
    }

    public function authStatus(){
      if(Controller::hasSession()){
        $this->authSuccess();
      }

      // This may break some users?
      if(!Input::get('shop') || !Input::get('hmac')){
        $this->authFail();
      }

      //Retrieve merchant details
      $merchantData = MerchantConfig::where('active', 1)
          ->where('shop_name', Input::get('shop'))
          ->first();

      // No install instance recorded
      if(!$merchantData){
          $this->authFail(false);
      }

      // Failed at some point
      if(!isset($merchantData->access_token) && isset($merchantData) && \Request::path() != 'callback'){
          die(Controller::$message_bag['INSTALLATION_ERROR']);
      }
      elseif(isset($merchantData->access_token) && isset($merchantData)) {
        $shopifyApi = new ShopifyApi; // Verify Hmac is valid - errors handled

        \Session::put('access_token', $merchantData->access_token);

        if(Controller::hasSession()){
          $this->authSuccess();
        }
      }

      $this->authFail(false); // Fallback
    }

    private function authSuccess($redirect = true){
      if($redirect){
        \Redirect::to('/index')->send();
      }
    }

    private function authFail($redirect = true){
      if($redirect){
        \Redirect::to('/redirect-to-install')->send();
      }
    }


    public function checkInstall(ShopifyAppAuth $ShopifyAppAuth){
      // Check we have the data we need
      if(!Input::get('hmac') ||
         !Input::get('shop') ||
         !Input::get('timestamp')){
        \App::abort(404);
      }

      return redirect($ShopifyAppAuth->installUrl());
    }

    public function access_token(ShopifyAppAuth $ShopifyAppAuth){
      // Check we have the data we need
      if(!Input::get('code') ||
         !Input::get('hmac') ||
         !Input::get('shop') ||
         !Input::get('timestamp')){
        \App::abort(404);
      }

      $ShopifyAppAuth->oAuth();

      return redirect('/');;
    }
}
