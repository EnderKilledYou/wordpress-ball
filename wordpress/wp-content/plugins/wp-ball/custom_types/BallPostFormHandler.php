<?php

class BallPostFormHandler {
	public static function EditSeason( WP_Post $post ): void {

		if ( $post->post_type !== strtolower( WPBallObjectsRepository::SEASON_POST_TYPE ) ) {
			return;
		}

		$players                = PlayerHelper::get_players();
		$current_players_scores = ScoreHelper::get_season_scores( $post->ID );

		?>
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle  ui-sortable-handle">Add Players to current season</h2>
                <div class="handle-actions hide-if-no-js">
                    <button type="button" class="button button-primary button-large"
                    > Rebuild with selected players
                    </button>

                </div>
            </div>
            <div class="inside ">
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


	private static function IsCurrentPlayer( WP_Post $player, array $current_players_scores ): bool {
		foreach ( $current_players_scores as $current_player_score ) {
			$player_id = ScoreHelper::get_player_id_from_score( $current_player_score->ID );
			if ( $player->ID === $player_id ) {
				return true;
			}
		}

		return false;
	}
}