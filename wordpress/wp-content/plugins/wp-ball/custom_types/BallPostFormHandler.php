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

		if ( strcasecmp( $post->post_type, WPBallObjectsRepository::MATCH_POST_TYPE ) !== 0 ) {
			return;
		}

		$player1_id = GameHelper::get_player1_ID( $post->ID );

		$player2_id    = GameHelper::get_player2_ID( $post->ID );
		$player1_title = get_the_title( $player1_id );
		$player2_title = get_the_title( $player2_id );
		$player1_score = GameHelper::get_player1_score( $post->ID );
		$player2_score = GameHelper::get_player2_score( $post->ID );
		?>
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle  ui-sortable-handle">Add Players to current season</h2>
                <div class="handle-actions hide-if-no-js">
                    <label>
						<?php echo $player1_title; ?>:
                        <input type="number" name="player1_score" value="<?php echo $player1_score; ?>">
                        <label>
                            Winner?
                            <input type="checkbox" name="winner_id" value="<?php echo $player1_id; ?>"/>
                        </label>
                    </label>
                    <label>
						<?php echo $player2_title; ?>:
                        <input type="number" name="player2_score" value="<?php echo $player2_score; ?>">
                        <label>
                            Winner?
                            <input type="checkbox" name="winner_id" value="<?php echo $player2_id; ?>"/>
                        </label>
                    </label>
                    <label>
                        Game Complete?
                        <input type="checkbox" name="game_complete"/>
                    </label>
                    <div class="buttons">
                        <button type="button" class="apply-filters button">Update Score<span></span></button>

                    </div>
                </div>
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

            </div>
        </div>

		<?php
	}
}