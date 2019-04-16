<?php

namespace EloquentMoneyPHP\Tests\Models;

use EloquentMoneyPHP\HasCurrency;
use Illuminate\Database\Eloquent\Model;

class JsonConfiguredModel extends Model
{
    use HasCurrency;

    public $currencies = [
        'amount' => 'json'
    ];

    public $fillable = [
        'amount'
    ];
}
