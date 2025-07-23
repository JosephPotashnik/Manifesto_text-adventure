<?php

require_once 'Choice.php';

class Room implements JsonSerializable
{
    public int $id;
    public string $flavorText;
    
    /** @var Choice[] */
    private array $choices;
    /** @var int[] */
    private array $connections; 
    //the connections to other rooms (ids of the other rooms)
    //note: connection is a directed edge, so if room A connects to room B, it does not mean that room B connects to room A

    public function __construct(int $id, string $flavorText, array $connections = [], array $choices = [])
    {
        $this->id = $id;
        $this->flavorText = $flavorText;
        //assumption: connections are unique and valid room IDs
        $this->connections = $connections;
        $this->choices = $choices;
    }

    // Implement JsonSerializable interface
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'flavorText' => $this->flavorText,
            'connections' => $this->connections,
            'choices' => array_map(function (Choice $choice) {
                return $choice->jsonSerialize();
            }, $this->choices)
        ];
    }

    // Create Room from array (for deserialization)
    public static function fromArray(array $data): Room
    {
        //assumption: the array contains 'id', 'flavorText', 'connections'
        //needs to add validation checks - beyond the scope of this home test, but noted here for completeness.
        return new Room(
            $data['id'],
            $data['flavorText'],
            $data['connections'] ?? [],
            array_map(function ($choiceData) {
                return Choice::fromArray($choiceData);
            }, $data['choices'] ?? [])
        );
    }

    public function getChoices(array $conditions): array
    {
        $filteredChoices = [];
        foreach ($this->choices as $choice) {

            $isDisplayed = $choice->getIsDisplayedMethod();
            if ($isDisplayed == null || $isDisplayed == '') {
                //if the isDisplayedMethod is null or empty, it means that the choice is always displayed
                $filteredChoices[] = $choice;
                continue;
            }
            else {
                $result  = $choice->executeDisplayChoice();
                if ($result) {
                    $filteredChoices[] = $choice;
                }
            } 
        }
        return $filteredChoices;
    }
}