<p align="center">
<img src="https://app.codeship.com/projects/58eaa2c0-4347-0137-78b4-0ad2fd259e46/status?branch=master" alt="Build Status">
<img src="https://coveralls.io/repos/github/ChrGriffin/eloquent-moneyphp/badge.svg?branch=master" alt="Coverage Status">
<img src="https://img.shields.io/github/license/chrgriffin/eloquent-moneyphp.svg" alt="License">
</p>

# Eloquent-MoneyPHP

Automatically cast Eloquent columns to MoneyPHP objects.

## Installation

Install Eloquent-MoneyPHP with composer:

```
composer install chrgriffin/eloquent-moneyphp
```

## Requirements

* PHP >= 7.1.3
* Laravel >= 5.6

This package does make one key assumption: that you are storing money in your database as integers, not floating point values. For example, eight dollars would be stored as `800`, instead of `8.00`. To find out why you should store currency and other floating point values this way, read more here.

## Usage

Usage is extremely simple. Eloquent-MoneyPHP provides a trait that can be used on any Eloquent model in conjunction with an array of column names:

```php
<?php

namespace App;

use EloquentMoneyPHP\HasCurrency;

class MyModel extends Model
{
    use HasCurrency;
    
    protected $currencies = [
        'total_usd' => 'USD',
        'total_eur' => 'EUR'
    ];
}
```

In the above setup, accessing the `total_usd` or `total_eur` attribute will automatically convert the attribute to a MoneyPHP object:

```php
<?php

$model = MyModel::find(1);
$total = $model->total; // <-- this will return a MoneyPHP object
```

Eloquent-MoneyPHP also supports storing a money amount as a json string in a text column. 

```json
{
  "amount": 800,
  "currency": "USD"
}
```

Then configure your model appropriately:

```php
<?php

namespace App;

use EloquentMoneyPHP\HasCurrency;

class MyModel extends Model
{
    use HasCurrency;
    
    protected $currencies = [
        'total' => 'json'
    ];
}
```

## Under the Hood

Eloquent-MoneyPHP makes use of the Laravel magic methods `getAttribute()` and `setAttribute()` in conjunction with the configured array of column names to determine if it should cast a given attribute to a MoneyPHP object.

This could obviously prove problematic if you are already implementing `getAttribute()` or `setAttribute()` yourself. Luckily, you can include the package behaviour in the methods yourself, if you need to:

```php
<?php

namespace App;

use EloquentMoneyPHP\HasCurrency;

class MyModel extends Model
{
    use HasCurrency;
    
    protected $currencies = [
        'total' => 'json'
    ];
    
    public function getAttribute($key)
    {
        if($this->attributeIsCurrency($key)) {
            return $this->getMoneyAttribute($key);
        }

        // the rest of your logic
    }
        
    public function setAttribute($key, $value)
    {
        if($this->attributeIsCurrency($key)) {
            return $this->setMoneyAttribute($key, $value);
        }

        // the rest of your logic
    }
}
```
