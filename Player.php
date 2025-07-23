<?php

/**
 * Represents a player in the text adventure game
 */
class Player
{
    public string $name;
    private int $hearts;
    public const INITIAL_HEARTS = 3;
    private array $decisions = []; // to store decisions made by the player
    private array $inventory = []; // to store items in the player's inventory
    
    /**
     * Initialize a new player
     * 
     * @param string $name The player's name
     * @param int $hearts The initial number of hearts (default: INITIAL_HEARTS)
     */
    public function __construct(string $name, int $hearts = self::INITIAL_HEARTS)
    {
        $this->name = $name;
        $this->hearts = $hearts;
    }

    /**
     * Adds hearts to the player's total.
     * @param int $hearts The number of hearts to add.
     * if the number is negative, it will decrease the hearts.      
     */
    public function addHearts(int $hearts): void
    {
        $this->hearts += $hearts;
        if ($hearts < 0) {
           echo "\033[32mYou lost " . abs($hearts) . " hearts. ";
        }
        else {
           echo "\033[32mYou gained " . $hearts . " hearts. ";
        }
        echo "You now have " . $this->hearts . " hearts left.\033[0m\n";
    }

    /**
     * Gets the current number of hearts
     * @return int
     */
    public function getHearts(): int
    {
        return $this->hearts;
    }

    /**
     * Add a decision to the player's decision history
     * 
     * @param string $decision The decision to add
     * @return void
     * @throws InvalidArgumentException If the decision already exists
     */
    public function addDecision(string $decision): void
    {
        if (!in_array($decision, $this->decisions, true)) {
            $this->decisions[] = $decision;
        } 
        else
        {
            throw new InvalidArgumentException("Decision '$decision' already exists.");
        }
    }

    /**
     * Check if the player does not have a specific decision
     * 
     * @param string $decision The decision to check
     * @return bool True if the player does not have the decision, false otherwise
     */
    public function doesNotHaveDecision(string $decision): bool 
    {
        return !$this->hasDecision($decision);
    }

    /**
     * Check if the player has made a specific decision
     * 
     * @param string $decision The decision to check
     * @return bool True if the player has the decision, false otherwise
     */
    public function hasDecision(string $decision): bool
    {
        return in_array($decision, $this->decisions, true);
    }

    /**
     * Add multiple decisions to the player's decision history
     * 
     * @param array $decisions Array of decisions to add
     * @return void
     * @throws InvalidArgumentException If any decision already exists
     */
    public function addDecisions(array $decisions): void
    {
        foreach ($decisions as $decision) {
            if (!in_array($decision, $this->decisions, true)) {
                $this->decisions[] = $decision;
            } 
            else
            {
                throw new InvalidArgumentException("Decision '$decision' already exists.");
            }
        }
    }

    /**
     * Add an item to the player's inventory
     * 
     * @param string $itemName The name of the item to add
     * @return void
     * @throws InvalidArgumentException If the item already exists in inventory
     */
    public function addInventoryItem(string $itemName): void
    {
        if (!in_array($itemName, $this->inventory, true)) {
            $this->inventory[] = $itemName;
            echo "\033[32mYou acquired " . $itemName . "\033[0m\n";        } 
        else
        {
            throw new InvalidArgumentException("Item '$itemName' already exists in inventory.");
        }
    }

    /**
     * Remove an item from the player's inventory
     * 
     * @param string $itemName The name of the item to remove
     * @return void
     * @throws InvalidArgumentException If the item is not found in inventory
     */
    public function removeInventoryItem(string $itemName): void
    {
        $key = array_search($itemName, $this->inventory, true);
        if ($key !== false) {
            unset($this->inventory[$key]);
            $this->inventory = array_values($this->inventory); // reindex the array
        } else {
            throw new InvalidArgumentException("Item '$itemName' not found in inventory.");
        }
    }

    /**
     * Check if the player does not have a specific inventory item
     * 
     * @param string $itemName The name of the item to check
     * @return bool True if the player does not have the item, false otherwise
     */
    public function doesNotHaveInventoryItem(string $itemName): bool 
    {
        return !$this->hasInventoryItem($itemName);
    }
    
    /**
     * Check if the player has a specific inventory item
     * 
     * @param string $itemName The name of the item to check
     * @return bool True if the player has the item, false otherwise
     */
    public function hasInventoryItem(string $itemName): bool
    {
        return in_array($itemName, $this->inventory, true);
    }

    /**
     * Get all decisions made by the player
     * 
     * @return array Array of all player decisions
     */
    public function getDecisions(): array
    {
        return $this->decisions;
    }

}