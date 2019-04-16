<?php

namespace EloquentMoneyPHP;

use EloquentMoneyPHP\Exceptions\InvalidArgumentException;
use Money\{ Money, Currency };

trait HasCurrency
{
    /**
     * @param $key
     * @return Money
     * @throws \Exception
     */
    public function getAttribute($key)
    {
        return $this->getMoneyAttribute($key);
    }

    /**
     * @param $key
     * @return Money
     * @throws \Exception
     */
    protected function getMoneyAttribute($key)
    {
        $value = parent::getAttribute($key);

        if(!$this->attributeIsCurrency($key)) {
            return $value;
        }

        if($this->currencies[$key] === 'json') {
            return $this->moneyFromJson(parent::getAttribute($key));
        }

        return new Money($value, new Currency($this->currencies[$key]));
    }

    /**
     * @param $json
     * @return Money
     * @throws \Exception
     */
    private function moneyFromJson($json) : Money
    {
        $json = json_decode($json, true) ?? [];
        if(!array_key_exists('amount', $json) || !array_key_exists('currency', $json)) {
            throw new InvalidArgumentException('JSON structure invalid.');
        }

        return new Money($json['amount'], new Currency($json['currency']));
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function setAttribute($key, $value)
    {
        return $this->setMoneyAttribute($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    protected function setMoneyAttribute($key, $value)
    {
        if(!$this->attributeIsCurrency($key)) {
            return parent::setAttribute($key, $value);
        }

        if(!$value instanceof Money){
            throw new InvalidArgumentException($key . ' must be an instance of ' . Money::class . '.');
        }

        if($this->currencies[$key] === 'json') {
            return parent::setAttribute($key, json_encode($value));
        }

        return parent::setAttribute($key, $value->getAmount());
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
