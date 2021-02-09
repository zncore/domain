<?php

namespace ZnCore\Domain\Interfaces\Entity;

interface ScenarioInterface
{

    public static function setScenario(string $scenario);
    public static function getScenario(): string;
    public static function isScenario(string $scenario): bool;
}
