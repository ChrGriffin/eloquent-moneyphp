<?php

namespace EloquentMoneyPHP\Tests\Models;

use EloquentMoneyPHP\HasCurrency;
use Illuminate\Database\Eloquent\Model;

final class JsonConfiguredModel extends Model
{
    use HasCurrency;

    public $currencies = [
        'amount' => 'json'
    ];

    public $fillable = [
        'amount'
    ];
}
