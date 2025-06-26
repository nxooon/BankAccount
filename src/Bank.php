<?php
declare(strict_types=1);

namespace App;

class Bank
{
    private Exchanger $exchanger;

    public function __construct()
    {
        $this->exchanger = new Exchanger();
        $this->exchanger->setRate('EUR', 'RUB', 80);
        $this->exchanger->setRate('USD', 'RUB', 70);
        $this->exchanger->setRate('EUR', 'USD', 1);
    }

    public function createAccount(): Account
    {
        return new Account($this->exchanger);
    }

    public function setExchangeRate(string $from, string $to, float $rate): void
    {
        $this->exchanger->setRate($from, $to, $rate);
    }
}