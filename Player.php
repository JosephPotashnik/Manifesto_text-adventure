<?php

class Player
{
    public string $name;
    private int $hearts;
    public const INITIAL_HEARTS = 3;
    private array $decisions = []; // to store decisions made by the player
    private array $inventory = []; // to store items in the player's inventory
    
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
           echo "You lost " . abs($hearts) . " hearts. ";
        }
        else {
           echo "You gained " . $hearts . " hearts. ";
        }
        echo "You now have " . $this->hearts . " hearts left.\n";
    }

    /**
     * Gets the current number of hearts
     * @return int
     */
    public function getHearts(): int
    {
        return $this->hearts;
    }

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
    public function doesNotHaveDecision(string $decision): bool 
    {
        return !$this->hasDecision($decision);
    }


    public function hasDecision(string $decision): bool
    {
        return in_array($decision, $this->decisions, true);
    }

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

    public function addInventoryItem(string $itemName): void
    {
        if (!in_array($itemName, $this->inventory, true)) {
            $this->inventory[] = $itemName;
        } 
        else
        {
            throw new InvalidArgumentException("Item '$itemName' already exists in inventory.");
        }
    }

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

    public function doesNotHaveInventoryItem(string $itemName): bool 
    {
        return !$this->hasInventoryItem($itemName);
    }
    
    public function hasInventoryItem(string $itemName): bool
    {
        return in_array($itemName, $this->inventory, true);
    }

    public function getDecisions(): array
    {
        return $this->decisions;
    }

}