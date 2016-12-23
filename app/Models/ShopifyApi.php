<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

class ShopifyApi extends Model
{
  public function __construct(){
    $this->config = [
      'api_key' => env('SHOPIFY_API_KEY'),
      'api_secret' => env('SHOPIFY_API_SECRET'),
      'redirect_url' => env('SHOPIFY_API_REDIRECT'),
      'permissions' => [
        'write_orders',
      ],
    ];

    $this->verifyHmac();
  }

  /*
   * Getter to retrieve config
   *
   * @return null
   */
  public function shopifyConfig(){
    return $this->config;
  }

  /*
   * Check hmac is equal to our hashed hmac
   *
   * @return null
   */
  public function verifyHmac(){
    foreach(Input::get() as $param => $value) {
      if ($param != 'signature' && $param != 'hmac') {
        $params[$param] = "{$param}={$value}";
      }
    }
    asort($params);

    $params = implode('&', $params);
    $hmac = Input::get('hmac');
    $calculatedHmac = hash_hmac('sha256', $params, $this->config['api_secret']);
    if($calculatedHmac !== Input::get('hmac')){
      \App::abort(400, 'HMAC does not match');
    }
    return;
  }
}
