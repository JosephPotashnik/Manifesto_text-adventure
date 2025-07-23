<?php

require_once 'Player.php';
require_once 'Outcome.php';

class Choice implements JsonSerializable
{
    public string $text;
    // here, this predicate is evaluated to two functions on the player object
    // HasInventoryItem(string $itemName) and HasDecision(string $decisionName)

    /** @var Outcome[] */
    private array $outcomes = [];
    private array $params;
    private string $isDisplayedMethod;

    private static EventMapper $mapper;

    public static function setEventMapper(EventMapper $e): void
    {
        self::$mapper = $e;
    }

    public function __construct(string $text, string $isDisplayedMethod, array $params = [], array $outcomes)
    {
        $this->text = $text;
        $this->params = $params;
        $this->isDisplayedMethod = $isDisplayedMethod; 
        $this->outcomes = $outcomes;
    }

    // Implement JsonSerializable interface
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'isDisplayedMethod' => $this->isDisplayedMethod,
            'params' => $this->params,
            'outcomes' => $this->outcomes ? array_map(function($outcome) {
                return $outcome->jsonSerialize();
            }, $this->outcomes) : []
        ];
    }

    public static function fromArray(array $data): Choice
    {
        $text = $data['text'] ?? '';
        $isDisplayedMethod = $data['isDisplayedMethod'] ?? '';
        $params = $data['params'] ?? [];
        $outcomes = array_map(function ($outcomeData) {
                return Outcome::fromArray($outcomeData);
            }, $data['outcomes'] ?? []);

        return new Choice($text, $isDisplayedMethod, $params, $outcomes);
    }

    public function getOutcomes(): array
    {
        return $this->outcomes;
    }

    public function getCallable(): callable
    {
        $target = self::$mapper->MapMethodNamesToTargets[$this->isDisplayedMethod];
        $callable = [$target, $this->isDisplayedMethod];
        return $callable;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getIsDisplayedMethod() : string //if string is null or empty, it means that the choice is always displayed
    {
        return $this->isDisplayedMethod;
    }

    public function executeDisplayChoice() : bool
    {
        return call_user_func_array($this->getCallable(), $this->params);
    }
}