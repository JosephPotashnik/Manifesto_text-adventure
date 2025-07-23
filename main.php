<?php

// Import the Player class
require_once 'Player.php';
require_once 'Game.php';
require_once 'EventMapper.php';
require_once 'Outcome.php';
require_once 'Choice.php';

echo "Welcome to our text adventure. What's your name? ";
$playerName = trim(fgets(STDIN)); 
$player = new Player($playerName);
$game = new Game($player);
$mapTargetNamesToTargets = [Player::class => $player, Game::class => $game];
$eventMapper = new EventMapper($mapTargetNamesToTargets);
Outcome::setEventMapper($eventMapper);
Choice::setEventMapper($eventMapper);
$game->start();
