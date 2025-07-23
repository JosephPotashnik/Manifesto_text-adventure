<?php

class EventMapper
{
    /** @var array<string, Player|Game> */
    public array $MapMethodNamesToTargets;

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
        ];
    }
}