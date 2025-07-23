<?php

/**
 * Maps method names to their target objects for dynamic method calls
 */
class EventMapper
{
    /** @var array<string, Player|Game> */
    public array $MapMethodNamesToTargets;

    /**
     * Initialize the event mapper with target objects
     * 
     * @param array $map Array mapping class names to their instances
     */
    public function __construct(array $map)
    {
        // These need to be static methods on the classes Player and Game
        $playerClassName = Player::class; // not Player::getClassName()
        $gameClassName = Game::class;

        // Now use those class names to fetch from the map
        $this->MapMethodNamesToTargets = [
            "doesNotHaveDecision"       => $map[$playerClassName],
            "hasDecision"               => $map[$playerClassName],
            "addDecision"               => $map[$playerClassName],
            "addHearts"                 => $map[$playerClassName],
            "addInventoryItem"          => $map[$playerClassName],
            "removeInventoryItem"       => $map[$playerClassName],
            "doesNotHaveInventoryItem"  => $map[$playerClassName],
            "hasInventoryItem"          => $map[$playerClassName],
            "moveToRoom"                => $map[$gameClassName],
            "gameWon"                   => $map[$gameClassName], //congrats!
            "gameLost"                  => $map[$gameClassName] //sudden death, etc.
        ];
    }
}