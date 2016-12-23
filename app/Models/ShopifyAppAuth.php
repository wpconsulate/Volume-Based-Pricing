<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Client;
use App\Models\MerchantConfig;
use App\Models\ShopifyApi;

class ShopifyAppAuth extends Model
{
    public function __construct(ShopifyApi $shopifyApi){
      $this->config = $shopifyApi->shopifyConfig();
    }

    /*
     * Build Url for Redirection to the install page
     *
     * @return String url to install app
     */
    public function installUrl()
    {
      //Check we have what we need
      if(!Input::get('shop')){
        \App::abort(400, 'Shop Name Not Found');
      }
      $this->shopName = Input::get('shop');
      $baseURL = "https://{$this->shopName}/admin/oauth/authorize";

      MerchantConfig::create(['shop_name' => $this->shopName]);

      // Build Url and Return
      $_partialUrl = [
        $baseURL,
        $this->appClientIdUri(),
        $this->appScopeUri(),
        $this->appRedirectUri(),
        $this->appStateTokenUri(),
      ];

      $url = '';
      foreach ($_partialUrl as $uri) {
        if($uri){
          $url .= $uri;
        }
      }

      return $url;
    }

    /*
     * URLify client that the app needs
     *
     * @return URI of client scope
     */
    private function appClientIdUri(){
      $uri = '';
      if(!empty($this->config['api_key'])){
        $uri  .= "?client_id=";
        $uri  .= $this->config['api_key'];
      }
      return $uri;
    }

    /*
     * URLify permission that the app needs
     *
     * @return URI of permission scope
     */
    private function appScopeUri(){
      $uri = '';
      if(!empty($this->config['permissions'])){
        $uri  .= "&scope=";
        $uri  .= implode(',', $this->config['permissions']);
      }
      return $uri;
    }

    /*
     * URLify redirection url
     *
     * @return URI of redirect url
     */
    private function appRedirectUri(){
      $uri = '';
      if(!empty($this->config['redirect_url'])){
        $uri  .= "&redirect_uri=";
        $uri  .= $this->config['redirect_url'];
      }
      return $uri;
    }

    /*
     * URLify redirection url
     *
     * @return URI of redirect url
     */
    private function appStateTokenUri(){
      $nonce = uniqid('VBP_');

      // Add nonce to DB
      MerchantConfig::where('active', 1)
          ->where('shop_name', $this->shopName)
          ->update(['nonce' => $nonce]);

      $uri = '';
      if(!empty($this->config['redirect_url'])){
        $uri  .= "&state=";
        $uri  .= $nonce;
      }

      return $uri;
    }

    /*
     * After permission given, app authenticates merchange store
     *
     * @return
     */
    public function oAuth(){

      // Errors handled in methods
      $this->checkShopUrlIsValid();
      $this->checkNonce();
      $this->checkHmac();

      $access_token = $this->getAccessToken();

      //Add Access token to DB
      MerchantConfig::where('active', 1)
          ->where('shop_name', Input::get('shop'))
          ->update(['access_token' => $access_token]);

      \Session::put('access_token', $access_token);

      return true;
    }

    /*
     * Check nonce is same as once sent before merchant gave permission
     *
     * @return null
     */
    private function checkNonce(){
      $nonceExists = MerchantConfig::where('active', 1)
        ->where('shop_name', Input::get('shop'))
        ->where('nonce', Input::get('state'))
        ->count();

      if($nonceExists !== 1){
        \App::abort(400, 'Nonce does not match');
      }
      return;
    }

    /*
     * Check hmac is equal to shopifys hashed hmac
     *
     * @return null
     */
    private function checkHmac(){
      $shopifyApi = new ShopifyApi;
    }

    /*
     * Check hmac is equal to shopifys hashed hmac
     *
     * @return null
     */
    private function checkShopUrlIsValid(){
      preg_match('/^[a-zA-Z0-9\-]+.myshopify.com$/', Input::get('shop'), $matches);

      // No match
      if(!$matches){
        \App::abort(400, 'Shop Url is invalid');
      }
      return;
    }

    /*
     * HTTP POST to Shopify to recieve the permanent access token
     *
     * @return string access_token
     */
    private function getAccessToken(){
      $baseUrl = "https://".Input::get('shop')."/admin/oauth/access_token";

      $headers = [
          'Content-Type' => 'application/json',
      ];
      $body = [

          'client_id'       => $this->config['api_key'],
          'client_secret'   => $this->config['api_secret'],
          'code'            => Input::get('code')
      ];

      $ch = curl_init($baseUrl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type' => 'application/xml',
      ]);

      return json_decode(curl_exec($ch))->access_token;
    }
}
