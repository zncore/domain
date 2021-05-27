<?php

namespace ZnCore\Domain\Interfaces\Entity;

/**
 * @deprecated
 */
interface ScenarioInterface
{

    public static function setScenario(string $scenario);
    public static function getScenario(): string;
    public static function isScenario(string $scenario): bool;
}
