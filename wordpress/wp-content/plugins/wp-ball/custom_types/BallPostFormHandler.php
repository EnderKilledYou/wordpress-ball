<?php

class BallPostFormHandler {
	public static function Edit_form_after_titles( WP_Post $post ): void {

		self::season_edit_form_after_title( $post );
		self::game_edit_form_after_title( $post );
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public static function game_edit_form_after_title( WP_Post $post ): void {

		if ( strcasecmp( $post->post_type, WPBallObjectsRepository::GAME_POST_TYPE ) !== 0 ) {
			return;
		}
		$game_index = 0;

		if ( isset( $_REQUEST['game_index'] ) && is_numeric( $_REQUEST['game_index'] ) ) {
			$game_index = (int) $_REQUEST['game_index'];
		}
		$player1_id    = GameHelper::get_player1_ID( $post->ID );
		$game_count    = GameHelper::get_game_count( $post->ID );
		$player2_id    = GameHelper::get_player2_ID( $post->ID );
		$player1_title = get_the_title( $player1_id );
		$player2_title = get_the_title( $player2_id );
		$player1_score = GameHelper::get_player1_score( $post->ID, $game_index );
		$player2_score = GameHelper::get_player2_score( $post->ID, $game_index );

		?>
        <div class="postbox ">
            <div class="postbox-header">


                <div class="inside">
                    <span></span>

                    <span class="">Set the Game Index  (<?php echo $game_index; ?> )to the game you want to see and hit publish.
                        <br/>Pick a game number to edit the scores. Pick Game result to edit the overall winner. <br/></span>
                    <label>
                        Game Index:
                        <select name="game_index" id="game_index">
							<?php
							$last_selected = "";
							if ( $game_index === - 1 ) {
								$last_selected = "selected";

							}
							for ( $i = 0; $i < $game_count; $i ++ ) {
								$selected = "";
								if ( $game_index === $i ) {
									$selected = "selected";
								}
								?>
                                <option <?php echo $selected; ?>
                                        value="<?php echo $i; ?>"><?php echo $i + 1; ?></option>
								<?php
							}
							?>
                            <option <?php echo $last_selected; ?> value="-1">Game Result</option>
                        </select>
                        <input type="button" name="update_selected_game" value="Swap Game"
                               onclick=' var searchParams = new URLSearchParams(window.location.search);
    searchParams.set("game_index", document.querySelector("#game_index").value);
                        window.location.search = searchParams.toString(); '/>
                    </label>
                    <br/>
					<?php if ( isset( $_REQUEST['game_index'] ) )
					{
					?>

                    <label>
						<?php echo $player1_title; ?>:
						<?php
						if ( $game_index >= 0 ) {
							?>
                            <input min="0" type="number" name="player1_score" value="<?php echo $player1_score; ?>">
							<?php
						}
						?>

                    </label>
                    <br/>
                    <label>
						<?php echo $player2_title; ?>:
						<?php
						if ( $game_index >= 0 ) {
							?>
                            <input min="0" type="number" name="player2_score" value="<?php echo $player2_score; ?>">
							<?php
						}
						?>

                    </label>


                    <label>
						<?php echo $player1_title; ?> Winner?
                        <input type="checkbox" name="winner_id" value="<?php echo $player1_id; ?>"/>
                    </label>
                    <label>
						<?php echo $player2_title; ?> Winner?
                        <input type="checkbox" name="winner_id" value="<?php echo $player2_id; ?>"/>
                    </label>
                    <br/>
                    <label>
                        Game Complete


                        <input type="checkbox" name="game_complete"/>
                    </label>

                </div>
				<?php
				}
				?>
            </div>
        </div>

		<?php
	}

	private static function IsCurrentPlayer( WP_Post $player, array $current_players_scores ): bool {
		foreach ( $current_players_scores as $current_player_score ) {
			$player_id = ScoreHelper::get_player_id_from_score( $current_player_score->ID );
			if ( $player->ID === $player_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public static function season_edit_form_after_title( WP_Post $post ): void {


		if ( strcasecmp( $post->post_type, WPBallObjectsRepository::SEASON_POST_TYPE ) !== 0 ) {
			return;
		}
		$players                = PlayerHelper::get_players();
		$current_players_scores = ScoreHelper::get_season_scores( $post->ID );
		?>
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle  ui-sortable-handle">Add Players to current season</h2>
                <div class="handle-actions hide-if-no-js">

                    <label>
                        <input type="checkbox" name="generate_matches" class=" "/>
                        Rebuild with selected players and start date:
                    </label>
                    <label>
                        <input type="date" name="start_date" value="<?php echo date( "m/d/yy" ); ?>">

                    </label>

                </div>
            </div>
            <div class=" inside ">
                <div id="postcustomstuff">

                </div>
                <p>
                    Players can be removed by removing the taxonomy for the season (same as the slug) from the
                    score. </p>
                <label>
                    <select multiple name="players[]">
						<?php
						foreach ( $players as $player ) {
							$isCurrent = self::IsCurrentPlayer( $player, $current_players_scores );
							$selected  = '';
							if ( $isCurrent ) {
								$selected = ' selected ';
							}
							?>

                            <option <?php echo $selected; ?> value="<?php echo $player->ID; ?>">
								<?php echo $player->post_title; ?>
                            </option>
						<?php }
						?>

                    </select>
                </label>
                <label>
                    Generate Total Games per week per player:
                    <input name="total_games" value="4">
                </label>
                <label>
                    Generate Total weeks:
                    <input name="total_weeks" value="7">
                </label>
            </div>
        </div>

		<?php
	}
}