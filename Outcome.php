<?php

require_once 'Player.php';

/**
 * Represents an outcome that occurs when a choice is selected
 */
class Outcome implements JsonSerializable
{
    public string $text;
    private array $params;
    private string $methodName;
    
    private static EventMapper $mapper;

    /**
     * Set the event mapper for mapping method names to targets
     * 
     * @param EventMapper $e The event mapper instance
     * @return void
     */
    public static function setEventMapper(EventMapper $e): void
    {
        self::$mapper = $e;
    }
    
    /**
     * Initialize a new outcome
     * 
     * @param string $text The descriptive text for this outcome
     * @param string $methodName The name of the method to execute for this outcome
     * @param array $params Parameters to pass to the method
     */
    public function __construct(string $text, string $methodName, array $params = [])
    {
        $this->text = $text;
        $this->params = $params;
        $this->methodName = $methodName; 
    }

    /**
     * Serialize the outcome to an array for JSON encoding
     * 
     * @return array The serialized outcome data
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'method' => $this->methodName,
            'params' => $this->params
        ];
    }
    
    /**
     * Execute the outcome's method with its parameters and display the outcome text
     * 
     * @return void
     */
    public function execute(): void
    {
        echo "{$this->text}\n";
        $target = self::$mapper->MapMethodNamesToTargets[$this->methodName];
        $callable = [$target, $this->methodName];
        call_user_func_array($callable, $this->params);
    }

    /**
     * Create an Outcome instance from array data
     * 
     * @param array $data The array data to deserialize
     * @return Outcome The created Outcome instance
     */
    public static function fromArray(array $data): Outcome
    {
        $text = $data['text'] ?? '';
        $method = $data['method'] ?? '';
        $params = $data['params'] ?? [];
        return new Outcome($text, $method, $params);
    }
}