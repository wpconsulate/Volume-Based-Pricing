<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\ShopifyApi;
use App\Models\ShopifyAppAuth;
use Illuminate\Support\Facades\Input;

class productController extends Controller
{
    public function index(ShopifyApi $shopifyApi){
      $shopifyApi->listProducts();
    }
}
