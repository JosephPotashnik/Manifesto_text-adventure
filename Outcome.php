<?php

require_once 'Player.php';
require_once 'EventMapper.php';

class Outcome implements JsonSerializable
{
    public string $text;
    private array $params;
    private string $methodName;

    private static EventMapper $mapper;

    public static function setEventMapper(EventMapper $e): void
    {
        self::$mapper = $e;
    }
    
    public function __construct(string $text, string $methodName, array $params = [])
    {
        $this->text = $text;
        $this->params = $params;
        $this->methodName = $methodName; 
    }

    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'method' => $this->methodName,
            'params' => $this->params
        ];
    }
    
    public function execute()
    {
        echo "{$this->text}\n";
        $target = self::$mapper->MapMethodNamesToTargets[$this->methodName];
        $callable = [$target, $this->methodName];
        call_user_func_array($callable, $this->params);
    }

    public static function fromArray(array $data): Outcome
    {
        $text = $data['text'] ?? '';
        $method = $data['method'] ?? '';
        $params = $data['params'] ?? [];
        return new Outcome($text, $method, $params);
    }
}
