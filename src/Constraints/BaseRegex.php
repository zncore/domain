<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

abstract class BaseRegex extends Regex
{

    public function __construct($pattern = [], string $message = null, string $htmlPattern = null, bool $match = null, callable $normalizer = null, array $groups = null, $payload = null, array $options = [])
    {
        if (is_array($pattern)) {
            $pattern['pattern'] = $this->regexPattern();
        } else {
            $pattern = $this->regexPattern();
        }
        parent::__construct($pattern, $message, $htmlPattern, $match, $normalizer, $groups, $payload, $options);
    }

    public function validatedBy()
    {
        return RegexValidator::class;
    }
}
