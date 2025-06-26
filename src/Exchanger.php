<?php
declare(strict_types=1);

namespace App;

class Exchanger
{
    private array $rates = [];

    public function setRate(string $from, string $to, float $rate): void
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        $this->rates[$from][$to] = $rate;
        $this->rates[$to][$from] = 1 / $rate;
    }

    public function getRate(string $from, string $to): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        if (!isset($this->rates[$from][$to])) {
            throw new \Exception("Такой курс не задан");
        }

        return $this->rates[$from][$to];
    }

    public function convert(float $amount, string $from, string $to): float
    {
        $rate = $this->getRate($from, $to);
        return $amount * $rate;
    }
}
