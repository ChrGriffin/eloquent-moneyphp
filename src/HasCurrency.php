<?php

namespace EloquentMoneyPHP;

use EloquentMoneyPHP\Exceptions\AttributeIsNotMoneyException;
use EloquentMoneyPHP\Exceptions\AttributeIsNotValidMoneyJsonException;
use Money\Money;
use Money\Currency;

trait HasCurrency
{
    /**
     * @return mixed|Money
     * @throws AttributeIsNotValidMoneyJsonException
     */
    public function getAttribute($key)
    {
        if($this->attributeIsMoney($key)) {
            return $this->getMoneyAttribute($key);
        }

        return parent::getAttribute($key);
    }

    /** @throws AttributeIsNotValidMoneyJsonException */
    protected function getMoneyAttribute($key): Money
    {
        $value = parent::getAttribute($key);
        return $this->currencies[$key] === 'json'
            ? $this->moneyFromJson(parent::getAttribute($key))
            : $this->moneyFromInteger($value, $this->currencies[$key]);
    }

    /** @throws AttributeIsNotValidMoneyJsonException */
    private function moneyFromJson(?string $json): Money
    {
        $json = json_decode($json, true) ?? [];
        if (!$this->moneyJsonIsValid($json)) {
            throw new AttributeIsNotValidMoneyJsonException(
                'JSON structure must have \'amount\' and \'currency\' keys.'
            );
        }

        return $this->moneyFromInteger($json['amount'], $json['currency']);
    }

    private function moneyFromInteger(int $amount, string $currency): Money
    {
        return new Money($amount, new Currency($currency));
    }

    public function moneyJsonIsValid(array $json): bool
    {
        return array_key_exists('amount', $json) && array_key_exists('currency', $json);
    }

    /** @throws AttributeIsNotMoneyException */
    public function setAttribute($key, $value)
    {
        if($this->attributeIsMoney($key)) {
            return $this->setMoneyAttribute($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    /** @throws AttributeIsNotMoneyException */
    protected function setMoneyAttribute($key, $value)
    {
        if (!$value instanceof Money) {
            throw new AttributeIsNotMoneyException($key . ' must be an instance of ' . Money::class . '.');
        }

        return $this->currencies[$key] === 'json'
            ? parent::setAttribute($key, json_encode($value))
            : parent::setAttribute($key, $value->getAmount());
    }

    public function attributeIsMoney($attribute): bool
    {
        return array_key_exists($attribute, $this->currencies ?? []);
    }
}
