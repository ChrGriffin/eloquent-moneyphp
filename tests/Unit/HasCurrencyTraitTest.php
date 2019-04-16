<?php

namespace EloquentMoneyPHP\Tests\Unit;

use EloquentMoneyPHP\Tests\{
    TestCase,
    Database\ConnectionResolver,
    Models\JsonConfiguredModel,
    Models\MagicAttributesOverriddenModel,
    Models\CurrencyMappedModel
};
use EloquentMoneyPHP\Exceptions\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

class HasCurrencyTraitTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Model::setConnectionResolver(new ConnectionResolver);
    }

    /**
     * @return void
     */
    public function testItDoesNotThrowExceptionWhenMakingNewModelUsingIt(): void
    {
        $this->assertNotEmpty(CurrencyMappedModel::make());
    }

    /**
     * @return void
     */
    public function testItConvertsMoneyToIntegerWhenSettingAmount(): void
    {
        $model = CurrencyMappedModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals(800, (int)$model->getAttributes()['amount']);
    }

    /**
     * @return void
     * @depends testItConvertsMoneyToIntegerWhenSettingAmount
     */
    public function testItConvertsIntegerToMoneyWhenGettingAmount(): void
    {
        $model = CurrencyMappedModel::make(['amount' => Money::USD(800)]);
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(800, $model->amount->getAmount());
    }

    /**
     * @return void
     */
    public function testItConvertsMoneyToJsonWhenSettingAmountThatIsConfiguredToUseJson(): void
    {
        $model = JsonConfiguredModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals(
            ['amount' => 800, 'currency' => 'USD'],
            json_decode($model->getAttributes()['amount'], true)
        );
    }

    /**
     * @return void
     * @depends testItConvertsMoneyToJsonWhenSettingAmountThatIsConfiguredToUseJson
     */
    public function testItConvertsJsonToMoneyWhenGettingAmountThatIsConfiguredToUseJson(): void
    {
        $model = JsonConfiguredModel::make(['amount' => Money::USD(800)]);
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(
            ['amount' => 800, 'currency' => 'USD'],
            json_decode($model->getAttributes()['amount'], true)
        );
    }

    /**
     * @return void
     */
    public function testItConvertsIntegerToMoneyWhenSettingAmountWhenOverridingGetAttributeMethod(): void
    {
        $model = MagicAttributesOverriddenModel::make(['amount' => Money::USD(800)]);
        $this->assertEquals('bananas', $model->someAttribute);
        $this->assertEquals(800, (int)$model->getAttributes()['amount']);
    }

    /**
     * @return void
     * @depends testItConvertsIntegerToMoneyWhenSettingAmountWhenOverridingGetAttributeMethod
     */
    public function testItConvertsIntegerToMoneyWhenGettingAmountWhenOverridingGetAttributeMethod(): void
    {
        $model = MagicAttributesOverriddenModel::make(['amount' => Money::USD(800)]);
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals('bananas', $model->someAttribute);
        $this->assertEquals(800, $model->amount->getAmount());
    }

    /**
     * @return void
     */
    public function testItThrowsExpectedExceptionWhenPassingNonMoneyValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CurrencyMappedModel::make(['amount' => 800]);
    }

    /**
     * @return void
     */
    public function testItThrowsExpectedExceptionWhenColumnJsonIsNotValid(): void
    {
        $model = JsonConfiguredModel::make();
        $model->getAttributes()['amount'] = json_encode(['invalid' => 'json']);

        $this->expectException(InvalidArgumentException::class);
        $model->amount;
    }

    /**
     * @return void
     */
    public function testItCorrectlyIdentifiesIfAColumnIsACurrency(): void
    {
        $model = CurrencyMappedModel::make();
        $this->assertTrue($model->attributeIsCurrency('amount'));
        $this->assertFalse($model->attributeIsCurrency('bananas'));
    }
}
