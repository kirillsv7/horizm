<?php

namespace App\Contracts;

interface RatingCalculatorInterface
{

    public function calculate(array $data): int;
}