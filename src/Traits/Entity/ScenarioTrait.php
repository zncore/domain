<?php

namespace ZnCore\Domain\Traits\Entity;

use ZnYii\Base\Enums\ScenarionEnum;

trait ScenarioTrait
{

    private static $_scenario = ScenarionEnum::CREATE;

    public static function setScenario(string $scenario)
    {
        self::$_scenario = $scenario;
    }

    public static function getScenario(): string
    {
        return self::$_scenario;
    }

    public static function isScenario(string $scenario): bool
    {
        return self::$_scenario == $scenario;
    }
}
