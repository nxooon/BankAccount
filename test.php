<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use App\Bank;

$bank = new Bank();
$account = $bank->createAccount();

//1
$account->createCurrency('usd');
$account->createCurrency('rub');
$account->createCurrency('eur');
$account->setDefaultCurrency('RUB');
echo implode(', ', $account->getCurrencies()), PHP_EOL, PHP_EOL;
$account->deposit('RUB', 1000);
$account->deposit('EUR', 50);
$account->deposit('USD', 50);

//2
echo $account->getTotalBalance(), PHP_EOL;
echo $account->getTotalBalance('usd'), PHP_EOL;
echo $account->getTotalBalance('eur'), PHP_EOL, PHP_EOL;

//3
$account->deposit('RUB', 1000);
$account->deposit('EUR', 50);
$account->withdraw('USD', 10);

//4
$bank->setExchangeRate('EUR', 'RUB', 150);
$bank->setExchangeRate('USD', 'RUB', 100);

//5
echo $account->getTotalBalance(), PHP_EOL, PHP_EOL;

//6
$account->setDefaultCurrency('EUR');
echo $account->getTotalBalance(), PHP_EOL, PHP_EOL;

//7
$account->convertCurrency(1000, 'RUB', 'EUR');
echo $account->getTotalBalance(), PHP_EOL, PHP_EOL;

//8
$bank->setExchangeRate('EUR', 'RUB', 120);

//9
echo $account->getTotalBalance(), PHP_EOL, PHP_EOL;

//10
$account->setDefaultCurrency('RUB');
$account->removeCurrency('EUR');
$account->removeCurrency('USD');
echo implode(', ', $account->getCurrencies()), PHP_EOL;
echo $account->getTotalBalance(), PHP_EOL;
