<?php

namespace Rainet\ImageBox\Box\Exceptions;

use Exception;

class FailedConversion extends Exception
{
    public static function invalidConversion(string $conversionName): self
    {
        return new static("Invalid conversion name `{$conversionName}`");
    }
}
