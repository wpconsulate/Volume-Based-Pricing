<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\authController;

class ShopifyApi extends Model
{

  private $url_product = [
    'all'         => '/admin/products.json?fields=id,images,title',
    'count'       => '/admin/products/count.json',
  ];

  /**
   * Verify hmac is correct and set curl opts
   */
  public function __construct(){
    if(!\Session::has('access_token') || !\Session::has('shop')){
      $shopifyAppAuth = new ShopifyAppAuth();
      $shopifyAppAuth->verifyHmac();
    }


    $this->requestHeaders = [
        'Content-Type' => 'application/xml',
    ];
    $this->requestBody = [
        'client_id'       => self::shopifyConfig()['api_key'],
        'client_secret'   => self::shopifyConfig()['api_secret']
    ];
  }

  /**
   * Set Shopify Config
   * @return Array Config
   */
  public static function shopifyConfig(){
    return [
      'api_key' => env('SHOPIFY_API_KEY'),
      'api_secret' => env('SHOPIFY_API_SECRET'),
      'redirect_url' => env('SHOPIFY_API_REDIRECT'),
      'permissions' => [
        'read_products',
        // 'write_orders',
      ],
    ];
  }
  public function buildUrl(){
    $url  = 'https://';
    $url .= self::shopifyConfig()['api_key'];
    $url .= ':';
    $url .= \Session::get('access_token');
    $url .= '@';
    $url .= \Session::get('shop');
    $url .= '/admin/';
    return $url;
  }
  public function listProducts(){
    $base = $this->buildUrl();

    $base .= 'products.json?limit=250';

    // $ch = curl_init($base);
    // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    // 'Content-Type: application/json',
    // ));
    // $server_output = curl_exec($ch);
    // curl_close ($ch);
    dd($base);
    $allProducts = json_decode(file_get_contents($base));

    return view('home');
  }


}
