<?php

namespace EloquentMoneyPHP;

use EloquentMoneyPHP\Exceptions\InvalidArgumentException;
use Money\{Money, Currency};

trait HasCurrency
{
    /**
     * @param $key
     * @return Money
     * @throws \Exception
     */
    public function getAttribute($key)
    {
        if($this->attributeIsCurrency($key)) {
            return $this->getMoneyAttribute($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * @param string $key
     * @return Money
     * @throws \Exception
     */
    protected function getMoneyAttribute($key): Money
    {
        $value = parent::getAttribute($key);
        return $this->currencies[$key] === 'json'
            ? $this->moneyFromJson(parent::getAttribute($key))
            : $this->moneyFromInteger($value, $this->currencies[$key]);
    }

    /**
     * @param null|string $json
     * @return Money
     * @throws InvalidArgumentException
     */
    private function moneyFromJson(?string $json): Money
    {
        $json = json_decode($json, true) ?? [];
        if (!$this->moneyJsonIsValid($json)) {
            throw new InvalidArgumentException('JSON structure invalid.');
        }

        return $this->moneyFromInteger($json['amount'], $json['currency']);
    }

    /**
     * @param int $amount
     * @param string $currency
     * @return Money
     */
    private function moneyFromInteger(int $amount, string $currency): Money
    {
        return new Money($amount, new Currency($currency));
    }

    /**
     * @param array $json
     * @return bool
     */
    public function moneyJsonIsValid(array $json): bool
    {
        return array_key_exists('amount', $json) && array_key_exists('currency', $json);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function setAttribute($key, $value)
    {
        if($this->attributeIsCurrency($key)) {
            return $this->setMoneyAttribute($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    protected function setMoneyAttribute($key, $value)
    {
        if (!$value instanceof Money) {
            throw new InvalidArgumentException($key . ' must be an instance of ' . Money::class . '.');
        }

        return $this->currencies[$key] === 'json'
            ? parent::setAttribute($key, json_encode($value))
            : parent::setAttribute($key, $value->getAmount());
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function attributeIsCurrency($attribute): bool
    {
        return array_key_exists($attribute, $this->currencies ?? []);
    }
}
