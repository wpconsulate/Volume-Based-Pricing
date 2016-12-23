<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantConfig extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['shop_name', 'access_token', 'nonce'];

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'merchant_config';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = true;

}
