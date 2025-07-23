<?php

require_once 'Choice.php';

/**
 * Represents a room in the text adventure game
 */
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

    /**
     * Initialize a new room
     * 
     * @param int $id The unique identifier for this room
     * @param string $flavorText The descriptive text shown when entering the room
     * @param array $connections Array of room IDs that this room connects to
     * @param array $choices Array of Choice objects available in this room
     */
    public function __construct(int $id, string $flavorText, array $connections = [], array $choices = [])
    {
        $this->id = $id;
        $this->flavorText = $flavorText;
        //assumption: connections are unique and valid room IDs
        $this->connections = $connections;
        $this->choices = $choices;
    }

    /**
     * Serialize the room to an array for JSON encoding
     * 
     * @return array The serialized room data
     */
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

    /**
     * Create a Room instance from array data
     * 
     * @param array $data The array data to deserialize
     * @return Room The created Room instance
     */
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

    /**
     * Get filtered choices based on player conditions
     * 
     * @param array $conditions Array of player conditions (currently unused but kept for interface compatibility)
     * @return array Array of Choice objects that should be displayed to the player
     */
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
                $result = $choice->executeDisplayChoice();
                if ($result) {
                    $filteredChoices[] = $choice;
                }
            } 
        }
        return $filteredChoices;
    }
}