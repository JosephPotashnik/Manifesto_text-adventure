<?php

require_once 'Player.php';
require_once 'Outcome.php';

/**
 * Represents a choice that a player can make in the game
 */
class Choice implements JsonSerializable
{
    public string $text;

    /** @var Outcome[] */
    private array $outcomes = [];
    private array $params;
    // isDisplayedMethod is a predicate evaluated to functions on the player object
    // e.g., HasInventoryItem(string $itemName) and HasDecision(string $decisionName)
    private string $isDisplayedMethod;

    private static EventMapper $mapper;

    /**
     * Set the event mapper for mapping method names to targets
     * 
     * @param EventMapper $eventMapper The event mapper instance
     * @return void
     */
    public static function setEventMapper(EventMapper $eventMapper): void
    {
        self::$mapper = $eventMapper;
    }

    /**
     * Initialize a new choice
     * 
     * @param string $text The display text for the choice
     * @param string $isDisplayedMethod The method to check if choice should be displayed
     * @param array $params Parameters for the display method
     * @param array $outcomes Array of outcomes when this choice is selected
     */
    public function __construct(string $text, string $isDisplayedMethod, array $params = [], array $outcomes)
    {
        $this->text = $text;
        $this->params = $params;
        $this->isDisplayedMethod = $isDisplayedMethod; 
        $this->outcomes = $outcomes;
    }

    /**
     * Serialize the choice to an array for JSON encoding
     * 
     * @return array The serialized choice data
     */
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

    /**
     * Create a Choice instance from array data
     * 
     * @param array $data The array data to deserialize
     * @return Choice The created Choice instance
     */
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

    /**
     * Get all outcomes associated with this choice
     * 
     * @return array Array of Outcome objects
     */
    public function getOutcomes(): array
    {
        return $this->outcomes;
    }

    /**
     * Get the callable function for the display method
     * 
     * @return callable The callable function
     */
    public function getCallable(): callable
    {
        $target = self::$mapper->MapMethodNamesToTargets[$this->isDisplayedMethod];
        $callable = [$target, $this->isDisplayedMethod];
        return $callable;
    }

    /**
     * Get the parameters for the display method
     * 
     * @return array The parameters array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get the display method name
     * If string is null or empty, it means that the choice is always displayed
     * 
     * @return string The display method name
     */
    public function getIsDisplayedMethod(): string
    {
        return $this->isDisplayedMethod;
    }

    /**
     * Execute the display choice method to determine if choice should be shown
     * 
     * @return bool True if choice should be displayed, false otherwise
     */
    public function executeDisplayChoice(): bool
    {
        return call_user_func_array($this->getCallable(), $this->params);
    }
}