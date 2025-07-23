<?php

// Import the Player class
require_once 'Player.php';
require_once 'Outcome.php';
require_once 'Choice.php';
require_once 'Room.php';

class State 
{
    private Player $player;
    private int $currentRoomId;

    public function __construct(Player $player, int $currentRoomId)
    {
        $this->player = $player;
        $this->currentRoomId = $currentRoomId;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getCurrentRoomId(): int
    {
        return $this->currentRoomId;
    }

    public function setCurrentRoom(int $roomId): void
    {
        $this->currentRoomId = $roomId;
    }
}

class Game
{
    private Player $player;

    /** @var Room[] */
    private array $rooms = [];
    private State $state;

    public function getCurrentRoom(): Room
    {
        return $this->rooms[$this->state->getCurrentRoomId()];
    }

    public function getAllRooms(): array
    {
        return $this->rooms;
    }

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->loadFromJsonFile();

    }

    public function saveToJsonFile(): bool
    {
        $filename = 'rooms.json';

        $roomsArray = [];
        foreach ($this->rooms as $room) {
            $roomsArray[] = $room->jsonSerialize();
        }

        $json = json_encode($roomsArray, JSON_PRETTY_PRINT);
        if ($json === false) {
            return false;
        }

        return file_put_contents($filename, $json) !== false;
    }

    // Deserialize rooms from JSON file
    public function loadFromJsonFile(): bool
    {
        $filename = 'rooms.json';

        if (!file_exists($filename)) {
            return false;
        }

        $json = file_get_contents($filename);
        if ($json === false) {
            return false;
        }

        $roomsData = json_decode($json, true);
        if ($roomsData === null) {
            return false;
        }

        $this->rooms = [];
        foreach ($roomsData as $roomData) {
            try 
            {
                $room = Room::fromArray($roomData, $this->player);
                $this->rooms[$room->id] = $room;
                //needs to add validation checks - beyond the scope of this home test, but noted here for completeness.


            } catch (Exception $e) {
                // Skip invalid room data
                continue;
            }
        }

        return true;
    }

    public function moveToRoom(int $roomId): void
    {
        $this->state->setCurrentRoom($roomId);
    }   

    public function start(): void
    {
        $this->state = new State($this->player, 1); //assumption: the index of the first room is 1.
        echo "You have " . $this->player->getHearts() . " hearts remaining.\n";

        while ($this->player->getHearts() > 0) 
        {
            $currentRoom = $this->getCurrentRoom();
            // validation check for current room, beyond the scope of this home test, but noted here for completeness.
            echo $currentRoom->flavorText . "\n";
            echo "Do you: \n";
            $choices = $currentRoom->getChoices($this->player->getDecisions());
            foreach ($choices as $index => $choice) {
                echo ($index + 1) . ". " . $choice->text . "\n";
            }

            $input = trim(fgets(STDIN));

            if (!is_numeric($input) || $input < 1 || $input > count($choices)) {
                echo "Invalid choice. Please try again.\n";
                continue;
            }

            $selectedChoice = $choices[$input - 1];
            $outcomes = $selectedChoice->getOutcomes();
            foreach ($outcomes as $outcome) 
            {
                $outcome->execute();

                if ($this->player->getHearts() <= 0) {
                    echo "You have no hearts left. Game over.\n";
                    break;
                }
            }
        }
    }
}
