<?php

// Import the Player class
require_once 'Player.php';
require_once 'Outcome.php';
require_once 'Choice.php';
require_once 'Room.php';

/**
 * Represents the current state of the game including player and room information
 */
class State 
{
    private Player $player;
    private int $currentRoomId;

    /**
     * Initialize a new game state
     * 
     * @param Player $player The player object
     * @param int $currentRoomId The ID of the current room
     */
    public function __construct(Player $player, int $currentRoomId)
    {
        $this->player = $player;
        $this->currentRoomId = $currentRoomId;
    }

    /**
     * Get the player object
     * 
     * @return Player The current player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Get the current room ID
     * 
     * @return int The ID of the current room
     */
    public function getCurrentRoomId(): int
    {
        return $this->currentRoomId;
    }

    /**
     * Set the current room by ID
     * 
     * @param int $roomId The ID of the room to move to
     * @return void
     */
    public function setCurrentRoom(int $roomId): void
    {
        $this->currentRoomId = $roomId;
    }
}

/**
 * Main game class that manages rooms, player state, and game flow
 */
class Game
{
    private Player $player;

    /** @var Room[] */
    private array $rooms = [];
    private State $state;

    /**
     * Get the current room the player is in
     * 
     * @return Room The current room object
     */
    public function getCurrentRoom(): Room
    {
        return $this->rooms[$this->state->getCurrentRoomId()];
    }

    /**
     * Get all available rooms in the game
     * 
     * @return array Array of all Room objects
     */
    public function getAllRooms(): array
    {
        return $this->rooms;
    }

    /**
     * Initialize a new game with a player
     * 
     * @param Player $player The player object
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->loadFromJsonFile();
    }

    /**
     * Save all rooms to a JSON file
     * 
     * @return bool True if save was successful, false otherwise
     */
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

    /**
     * Load rooms from JSON file and deserialize them
     * 
     * @return bool True if load was successful, false otherwise
     */
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

    /**
     * Move the player to a specific room
     * 
     * @param int $roomId The ID of the room to move to
     * @return void
     */
    public function moveToRoom(int $roomId): void
    {
        $this->state->setCurrentRoom($roomId);
    }   

    /**
     * Start the game loop and handle player interactions
     * 
     * @return void
     */
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
