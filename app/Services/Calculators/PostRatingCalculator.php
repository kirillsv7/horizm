<?php

namespace App\Services\Calculators;

use App\Contracts\RatingCalculatorInterface;

class PostRatingCalculator implements RatingCalculatorInterface
{

    public function calculate(array $data): int
    {
        return str_word_count($data['title']) * 2 + str_word_count($data['body']);
    }
}