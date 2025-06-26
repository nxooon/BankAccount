<?php
declare(strict_types=1);

namespace App;

class Account
{
    private string $defaultCurrency;
    private array $currencies = [];
    private Exchanger $exchanger;

    public function __construct(Exchanger $exchanger)
    {
        $this->exchanger = $exchanger;
    }

    public function createCurrency(string $code): void
    {
        $code = strtoupper($code);
        if (!isset($this->currencies[$code])) {
            $this->currencies[$code] = new Currency($code);
        }
    }

    public function setDefaultCurrency(string $code): void
    {
        $currency = $this->getCurrency($code);
        $this->defaultCurrency = $currency->getCode();
    }

    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    public function deposit(string $code, float $amount): void
    {
        $this->getCurrency($code)->addBalance($amount);
    }

    public function withdraw(string $code, float $amount): void
    {
        $this->getCurrency($code)->withdrawBalance($amount);
    }

    public function getCurrencies(): array
    {
        return array_keys($this->currencies);
    }

    public function getCurrencyBalance(?string $code = null): string
    {
        $currency = $this->getCurrency($code ?? $this->defaultCurrency);
        $balance = sprintf("%.2f %s", $currency->getBalance(), $currency->getCode());
        return $balance;
    }

    public function getTotalBalance(?string $code = null): string
    {
        $totalBalance = 0;
        $asCurrency = strtoupper($code ?? $this->defaultCurrency);
        foreach ($this->currencies as $key => $currency) {
            $amount = $currency->getBalance();
            $converted = $this->exchanger->convert($amount, $key, $asCurrency);
            $totalBalance += $converted;
        }
        return sprintf("%.2f %s", $totalBalance, $asCurrency);
    }

    public function getCurrency(string $code): Currency
    {
        $code = strtoupper($code);
        if (!isset($this->currencies[$code])) {
            throw new \Exception("Валюта $code не найдена");
        }
        return $this->currencies[$code];

    }

    public function convertCurrency(float $amount, string $from, string $to): void
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        $this->getCurrency($from)->withdrawBalance($amount);
        $converted = $this->exchanger->convert($amount, $from, $to);
        $this->getCurrency($to)->addBalance($converted);
    }

    public function removeCurrency(string $code): void
    {
        $balance = $this->getCurrency($code)->getBalance();

        if ($balance > 0) {
            $converted = $this->exchanger->convert($balance, $code, $this->defaultCurrency);
            $this->currencies[$this->defaultCurrency]->addBalance($converted);
        }

        unset($this->currencies[$code]);
    }
}