<?php

namespace EloquentMoneyPHP\Tests\Models;

use EloquentMoneyPHP\HasCurrency;
use Illuminate\Database\Eloquent\Model;

final class MagicAttributesOverriddenModel extends Model
{
    use HasCurrency;

    /**
     * @var array
     */
    public $currencies = [
        'amount' => 'USD'
    ];

    /**
     * @var array
     */
    public $fillable = [
        'amount'
    ];

    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function getAttribute($key)
    {
        if($this->attributeIsMoney($key)) {
            return $this->getMoneyAttribute($key);
        }

        return 'bananas';
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed|string
     * @throws \Exception
     */
    public function setAttribute($key, $value)
    {
        if($this->attributeIsMoney($key)) {
            return $this->setMoneyAttribute($key, $value);
        }

        return 'pineapples';
    }
}
