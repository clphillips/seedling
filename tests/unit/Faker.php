<?php
namespace Seedling\tests\unit;

class Faker
{
    public function numberBetween($min, $max)
    {
        return mt_rand($min, $max);
    }
}
