<?php

class BallPostFormHandler {
	public static function Edit_form_after_titles( WP_Post $post ): void {

		self::season_edit_form_after_title( $post );
		self::game_edit_form_after_title( $post );
		self::player_edit_form_after_title( $post );
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public static function player_edit_form_after_title( WP_Post $post ): void {
		if ( strcasecmp( $post->post_type, WPBallObjectsRepository::PLAYER_POST_TYPE ) !== 0 ) {
			return;
		}
		$user_id = PlayerHelper::get_player_user_id( $post->ID );
		if ( $user_id ) {
			$user = get_userdata( $user_id );
			if ( $user ) {
				echo "This Account has been assigned to " . $user->display_name;

				return;
			}
		}

		$users = get_users();


		?>
        <label>
            Pick a user to assign and hit publish. You can only do this once so make sure you get it right.
            <select name="player_to_assign">
				<?php
				foreach ( $users as $user ) {
					?>
                    <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
				<?php } ?>

            </select>
        </label>
		<?php
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
		$game_id         = $post->ID;
		$match_id        = GameHelper::get_match_id( $game_id );
		$match_date_week = MatchHelper::get_week( $match_id );
		$date_week       = date( "W" );
//		if ( $date_week !== $match_date_week ) {
//			?>
        <!--            <h2>This Match is not open yet</h2>-->
        <!---->
        <!--			--><?php
//            return;
//		}
		$player1_id    = GameHelper::get_player1_ID( $game_id );
		$game_count    = GameHelper::get_game_count( $game_id );
		$player2_id    = GameHelper::get_player2_ID( $game_id );
		$player1_title = get_the_title( $player1_id );
		$player2_title = get_the_title( $player2_id );
		$player1_score = GameHelper::get_player1_score( $game_id, $game_index );
		$player2_score = GameHelper::get_player2_score( $game_id, $game_index );
		$game_state    = GameHelper::is_game_complete( $game_id, $game_index );


		$game_state_all = GameHelper::is_game_complete( $game_id, - 1 );
		if ( $game_state_all ) {
			?>
            <h2>All game are complete</h2>
			<?php
		}
		?>
        <div class="postbox ">
            <div class="postbox-header">


                <div class="inside">
                    <span></span>

                    <span class="">Set the Game Index  to the game you want to see and hit publish.
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
                        <input type="button" name="update_selected_game" value="Set Game To Score"
                               onclick=' var searchParams = new URLSearchParams(window.location.search);
    searchParams.set("game_index", document.querySelector("#game_index").value);
                        window.location.search = searchParams.toString(); '/>
                    </label>
                    <br/>
					<?php if ( ! $game_state && isset( $_REQUEST['game_index'] ) )
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


                    <br/>
                    <label>
                        Game Complete


                        <input type="checkbox" name="game_complete"/>
                    </label>

                </div>
				<?php
				}
				else if ( $game_state ) {
					?>
                    <h3>This game is complete.</h3>
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
		$seasons                = PlayerHelper::get_seasons();
		$current_players_scores = ScoreHelper::get_season_scores( $post->ID );
		?>
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle  ui-sortable-handle">Add Players to current season</h2>
                <div>
                    <span>Add players first to use then use the rebuild options. </span> <br/> <span>Make sure you have a <b>POST TITLE</b></span>
                </div>

            </div>
            <div class=" inside ">


                <div>
                    <label>
                        Players to add to this auto generation.
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
                </div>
				<?php if ( count( $current_players_scores ) > 0 ) { ?>


                    <div>
                        <label>
                            Use these
                            <select multiple name="seasons[]">
								<?php
								foreach ( $seasons as $season ) {

									if ( $season->ID === $post->ID ) {
										continue;
									}

									?>

                                    <option value="<?php echo $season->ID; ?>">
										<?php echo $season->post_title; ?>
                                    </option>
								<?php }
								?>

                            </select>
                        </label>
                    </div>
                    <div>
                        <label>
                            Generate these as finals week with people closest in standing?
                            <input name="generate_finals" type="checkbox" value="yes">
                        </label>
                    </div>

                    <div>
                        <label>
                            How many rounds per game:
                            <input name="total_games" value="3">
                        </label>
                    </div>
                    <div>
                        <label>
                            How many weeks:
                            <input name="total_weeks" value="7">
                        </label>
                    </div>
                    <div>
                        <label>
                            <input type="date" name="start_date" value="<?php echo date( "m/d/yy" ); ?>">

                        </label>
                    </div>
                    <div>
                        <label>
                            <input type="checkbox" name="generate_matches" class=" "/>
                            Generate Matches using the selected date above
                        </label>
                    </div>
				<?php } ?>
            </div>
        </div>

		<?php
	}
}