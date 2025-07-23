# Option 2: Backend Challenge  
**Building a Text Adventure Game**

---

## How to Run

```bash
php main.php
```

---

## Developer Note

I had no prior experience with PHP before this challenge. However, as a seasoned C# developer, I found that the syntactic and structural similarities—especially with PHP’s latest typed features—allowed me to quickly bridge the gap. With some focused exploration, I was able to gain the necessary understanding to tackle this challenge effectively

## General Approach

The central principle guiding this implementation was **clean separation between game logic and game content**. That is, no hardcoded rooms, inventory items, decisions, or outcomes are embedded in the engine. Instead, the engine consumes external content (e.g., a JSON file) and drives the game accordingly.

This approach allows easy swapping of different adventures without changing the core code.

For example, a room might be described like this:

```json
{
  "id": 1,
  "flavorText": "You stand before the imposing gates of an ancient castle. The iron gates creak in the wind, and you notice a rusty key lying on the ground nearby.",
  "connections": [2],
  "choices": [
    {
      "text": "Pick up the rusty key",
      "isDisplayedMethod": "doesNotHaveDecision",
      "params": ["took_key"],
      "outcomes": [
        {
          "text": "You pick up the rusty key and slip it into your pocket.",
          "method": "addInventoryItem",
          "params": ["rusty_key"]
        },
        {
          "text": "",
          "method": "addDecision",
          "params": ["took_key"]
        }
      ]
    },
    {
      "text": "Enter the castle courtyard",
      "isDisplayedMethod": "",
      "params": [],
      "outcomes": [
        {
          "text": "You push through the gates and enter the castle courtyard.",
          "method": "moveToRoom",
          "params": [2]
        }
      ]
    }
  ]
}
```

The game loads this content (from `rooms.json`) during the initialization of the `Player` object.

---

## Design Overview

The engine follows a classic object-oriented design:

- `Game`, `Player`, `Room`, `Choice`, and `Outcome` classes manage core functionality.
- An `EventMapper` class dynamically maps method names (e.g. `addInventoryItem`) to actual PHP callables.

Game designers use method names in JSON to specify:
- **Outcomes**: What happens after a choice (e.g., `moveToRoom`, `addHearts`).
- **Conditions**: Whether a choice is available (e.g., `hasDecision`, `hasInventoryItem`).

---

## Features Supported

- Room branching and navigation  
- Inventory system  
- Decision tracking  
- Conditional choices based on inventory or past decisions  
- Easily extensible content model

---

## Work in Progress / Limitations

- **Saving state**: Can be implemented by serializing the `Player` and `Game` objects.
- **Input history saving**: Straightforward via logging of keypress events.
- **Error handling**: Currently assumes valid input and logically consistent `rooms.json`.
- **Compound predicates**: Currently, choices rely on a single predicate (e.g., `hasInventoryItem`).  
  More complex logic (like `hasItem('bow') && !hasDecision('defeated_dragon')`) is not yet supported.  
  This would require **composable predicates** (likely represented as a binary tree of logical operations).  
  Right now, such cases may cause game-breaking contradictions (e.g., fighting a boss multiple times).
- **Room Flavour text changes**: Currently, room flavour text is unconditional. If it describes transitory elements such as inventory or what happens before a decision which takes place in it, it might become irrelevant. Can be adapted in the same way that Choices are displayed. Game designers can be careful not to include conditional elements in the room's flavour text but I leave it here as it is beyond the scope of the exercise.

---

Hope you enjoy the adventure!

**P.S.** I definitely caught the *Discworld* reference—I've read all 41. 🐢🌍
