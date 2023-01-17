# DO NOT INSTALL THIS VERSION IT IS NOT TESTED OR DO SO AT YOUR OWN RISK MAY CRASH OR CORRUPT DATA STILL WRITING TESTING.
# Wordpress Ball

## Set up

1. First add some users (not players)
2. Add some players, assign users to player
3. Add a season, pick some players hit publish
4. enjoy auto generated season
5. To add a custom match/game create a season but don't auto generate
   1. Create a match and set season_id, week and date in the custom fields to the id of the season
   2. Create some games and set match_id, game_count, player1_id, player2_id, and others from the drop downs in custom fields
      1. It's probably best to copy an existing game and just change the match_id and player_ids! That way you know you have all the scores.

## Scoring a Game
   1. There will be a drop down with the round number (game index). Set that and hit the button.
   2. You can update the player scores as much as you want until the game is over. Then select the winner and game complete and hit publish
   3. Repeat until no games left. Then set the OVERALL winner by selecting "Game Result" as the round 
# Short Codes
### [leader_board]
No parameters displays the over all leader board


### [season_leader_board] 
optional season_id when not on a season post displays the season leader board

### [player_season_stats]
required season_id, 
optional player_id when not on a player post displays the player's season stats

### [player_stats]
optional player_id when not on a player post displays the player stats

### [game_table]
#### a single game consisting of several rounds of play
optional game_id when not on a game post displays score for that game

### [match_table]
#### The Week table
optional match_id when not on a match post displays all the games for that match
optional match_index  When set, displays only the games in the particular match (group)
optional game_index when set, displays the game number. for example, if you only want to show 
game 3 then set this to three. When combined with match_index it will only show game 3 of the selected
match index. By itself it will show game 3 of all matches in that week. 

### [season_table]
#### A group of weekly matches
optional season_id when not on a season post displays the matches for the season

### [playerwins]
optional player_id when not on a player post displays the total wins of the player

### [playerscore]
optional player_id when not on a player post displays the sum of all the players points

### [playerlosses]
optional player_id when not on a player post displays the total losses of the player

For devs:
Quick Setup:

Clone repo to the root of your PhpStorm Project.

In PhpStorm:

Preferences->PHP and add a new CLI Interpreter.

Add new from Docker.

Select Docker Compose, then select the `docker-compose.yml` in the cloned folder.

Select `wordpress` in the services select.

Click OK.

## Plugin Code

### Repos

Each concept is broken down in it's own static repo helper.
There is some coupling with concepts th

