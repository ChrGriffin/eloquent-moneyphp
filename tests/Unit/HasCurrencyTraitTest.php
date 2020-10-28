<?php

namespace EloquentMoneyPHP\Tests\Unit;

use EloquentMoneyPHP\Tests\TestCase;
use EloquentMoneyPHP\Tests\Database\ConnectionResolver;
use EloquentMoneyPHP\Tests\Models\JsonConfiguredModel;
use EloquentMoneyPHP\Tests\Models\MagicAttributesOverriddenModel;
use EloquentMoneyPHP\Tests\Models\CurrencyMappedModel;
use EloquentMoneyPHP\Exceptions\AttributeIsNotMoneyException;
use EloquentMoneyPHP\Exceptions\AttributeIsNotValidMoneyJsonException;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

class HasCurrencyTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Model::setConnectionResolver(new ConnectionResolver);
    }

    public function testItDoesNotThrowExceptionWhenMakingNewModelUsingIt(): void
    {
        $this->assertNotEmpty(CurrencyMappedModel::make());
    }

    public function testItConvertsMoneyToIntegerWhenSettingAmount(): void
    {
        $model = CurrencyMappedModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals(800, (int)$model->getAttributes()['amount']);
    }

    /** @depends testItConvertsMoneyToIntegerWhenSettingAmount */
    public function testItConvertsIntegerToMoneyWhenGettingAmount(): void
    {
        $model = CurrencyMappedModel::make(['amount' => Money::USD(800)]);
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(800, $model->amount->getAmount());
    }

    public function testItConvertsMoneyToJsonWhenSettingAmountThatIsConfiguredToUseJson(): void
    {
        $model = JsonConfiguredModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals(
            ['amount' => 800, 'currency' => 'USD'],
            json_decode($model->getAttributes()['amount'], true)
        );
    }

    /** @depends testItConvertsMoneyToJsonWhenSettingAmountThatIsConfiguredToUseJson */
    public function testItConvertsJsonToMoneyWhenGettingAmountThatIsConfiguredToUseJson(): void
    {
        $model = JsonConfiguredModel::make(['amount' => Money::USD(800)]);
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(
            ['amount' => 800, 'currency' => 'USD'],
            json_decode($model->getAttributes()['amount'], true)
        );
    }

    public function testItConvertsIntegerToMoneyWhenSettingAmountWhenOverridingGetAttributeMethod(): void
    {
        $model = MagicAttributesOverriddenModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals('bananas', $model->someAttribute);
        $this->assertEquals(800, (int)$model->getAttributes()['amount']);
        $this->assertInstanceOf(Money::class, $model->amount);
    }

    /** @depends testItConvertsIntegerToMoneyWhenSettingAmountWhenOverridingGetAttributeMethod */
    public function testItConvertsIntegerToMoneyWhenGettingAmountWhenOverridingGetAttributeMethod(): void
    {
        $model = MagicAttributesOverriddenModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals('bananas', $model->someAttribute);
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(800, $model->amount->getAmount());
    }

    public function testItThrowsExpectedExceptionWhenPassingNonMoneyValue(): void
    {
        $this->expectException(AttributeIsNotMoneyException::class);
        CurrencyMappedModel::make(['amount' => 800]);
    }

    public function testItThrowsExpectedExceptionWhenColumnJsonIsNotValid(): void
    {
        $model = JsonConfiguredModel::make();
        $model->getAttributes()['amount'] = json_encode(['invalid' => 'json']);

        $this->expectException(AttributeIsNotValidMoneyJsonException::class);
        $model->amount;
    }

    public function testItCorrectlyIdentifiesIfAColumnIsACurrency(): void
    {
        $model = CurrencyMappedModel::make();
        $this->assertTrue($model->attributeIsMoney('amount'));
        $this->assertFalse($model->attributeIsMoney('bananas'));
    }
}
