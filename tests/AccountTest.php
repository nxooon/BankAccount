<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\Account;
use App\Exchanger;

class AccountTest extends TestCase
{
    private function createExchanger(): Exchanger
    {
        $exchanger = new Exchanger();
        $exchanger->setRate('USD', 'RUB', 70);
        $exchanger->setRate('EUR', 'RUB', 80);
        $exchanger->setRate('EUR', 'USD', 1);

        return $exchanger;
    }
    private function createAccountWithCurrencies(): Account
    {
        $account = new Account($this->createExchanger());
        $account->createCurrency('USD');
        $account->createCurrency('RUB');
        $account->createCurrency('EUR');

        return $account;
    }

    public static function withdrawProvider(): array
    {
        return [
            'more than balance'  => [100, 'Недостаточно средств в EUR'],
            'negative withdraw' => [-100, 'Списание не может быть отрицательным'],
        ];
    }

    public static function totalBalanceProvider(): array
    {
        return [
            ['RUB', 1000, 50, 50, '8500.00 RUB'],
            [null, 1000, 50, 50, '8500.00 RUB'],
            ['RUB', 0, 0, 0, '0.00 RUB'],
            ['USD', 0, 100, 0, '100.00 USD'],
            ['USD', 700, 0, 10, '20.00 USD'],
            ['EUR', 1600, 0, 0, '20.00 EUR']
        ];
    }

    public function testCreateCurrency(): void
    {
        $account = new Account($this->createExchanger());
        $account->createCurrency('USD');
        $account->createCurrency('EUR');
        $this->assertContains('USD', $account->getCurrencies());
        $this->assertContains('EUR', $account->getCurrencies());
        $this->assertNotContains('RUB', $account->getCurrencies());

    }

    public function testSetDefaultCurrencyException(): void
    {
        $this->expectException(Exception::class);

        $exchanger = new Exchanger();
        $account = new Account($exchanger);
        $account->setDefaultCurrency('USD');
    }

    public function testSetDefaultCurrency(): void
    {
        $account = $this->createAccountWithCurrencies();
        $account->setDefaultCurrency('USD');

        $this->assertSame('USD', $account->getDefaultCurrency());
    }

    public function testDepositAndWithdraw(): void
    {
        $account = $this->createAccountWithCurrencies();
        $account->deposit('USD', 100);
        $account->withdraw('USD', 40);

        $this->assertSame('60.00 USD', $account->getCurrencyBalance('USD'));
    }

    #[DataProvider('withdrawProvider')]
    public function testWithdrawMoreThanBalanceException($amount, $exception): void
    {
        $this->expectExceptionMessage($exception);

        $account = $this->createAccountWithCurrencies();
        $account->deposit('EUR', 50);
        $account->withdraw('EUR', $amount);
    }

    #[DataProvider('totalBalanceProvider')]
    public function testTotalBalance($code, $rub, $usd, $eur, $expected): void
    {
        $account = $this->createAccountWithCurrencies();
        $account->setDefaultCurrency('RUB');
        $account->deposit('RUB', $rub);
        $account->deposit('USD', $usd);
        $account->deposit('EUR', $eur);

        $this->assertSame($expected, $account->getTotalBalance($code));
    }

    public function testConvertCurrency(): void
    {
        $account = $this->createAccountWithCurrencies();
        $account->deposit('USD', 100);
        $account->convertCurrency(50, 'USD', 'RUB');

        $usd = $account->getCurrencyBalance('USD');
        $rub = $account->getCurrencyBalance('RUB');

        $this->assertSame('50.00 USD', $usd);
        $this->assertSame('3500.00 RUB', $rub);
    }

    public function testRemoveCurrencyConvertsAndRemoves(): void
    {
        $account = $this->createAccountWithCurrencies();
        $account->setDefaultCurrency('RUB');
        $account->deposit('EUR', 10);
        $account->removeCurrency('EUR');

        $this->assertNotContains('EUR', $account->getCurrencies());

        $this->assertSame('800.00 RUB', $account->getCurrencyBalance('RUB'));
    }
}