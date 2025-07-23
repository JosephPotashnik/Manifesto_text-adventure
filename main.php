<?php

/**
 * Main entry point for the text adventure game
 * 
 * This file initializes the game components and starts the game loop.
 * It handles player creation, game setup, and event mapping configuration.
 */

// Import required classes
require_once 'Player.php';
require_once 'Game.php';
require_once 'EventMapper.php';
require_once 'Outcome.php';
require_once 'Choice.php';

// Get player name from user input
echo "Welcome to our text adventure. What's your name? ";
$playerName = trim(fgets(STDIN)); 

// Initialize game components
$player = new Player($playerName);
$game = new Game($player);

// Set up event mapping for dynamic method calls
$classToInstanceMap = [Player::class => $player, Game::class => $game];
$eventMapper = new EventMapper($classToInstanceMap);

// Configure event mapper for outcome and choice classes
Outcome::setEventMapper($eventMapper);
Choice::setEventMapper($eventMapper);

// Start the game
$game->start();
