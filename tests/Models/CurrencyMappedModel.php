<?php

namespace EloquentMoneyPHP\Tests\Models;

use EloquentMoneyPHP\HasCurrency;
use Illuminate\Database\Eloquent\Model;

class CurrencyMappedModel extends Model
{
    use HasCurrency;

    public $currencies = [
        'amount' => 'USD'
    ];

    public $fillable = [
        'amount'
    ];
}
