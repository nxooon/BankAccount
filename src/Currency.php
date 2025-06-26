<?php
declare(strict_types=1);

namespace App;
class Currency
{
    private string $code;
    private float $balance = 0;

    public function __construct(string $code){
        $this->code = strtoupper($code);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function addBalance(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Пополнение не может быть отрицательным');
        }
        $this->balance += $amount;
    }

    public function withdrawBalance(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Списание не может быть отрицательным');
        }
        if ($amount > $this->balance) {
            throw new \Exception("Недостаточно средств в {$this->code}");
        }
        $this->balance -= $amount;
    }
}