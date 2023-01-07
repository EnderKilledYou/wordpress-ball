<?php

class BallPostFormHandler {
	public static function EditSeason( WP_Post $post ): void {


		$players         = PlayerHelper::get_players();
		$current_players = ScoreHelper::get_season_scores( $post->ID );
        var_dump($current_players);
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
                            $isCurrent = self::IsCurrentPlayer( $player, $current_players );
                            $selected = '';
                            if($isCurrent){
                                $selected = ' selected ';
                            }
                            ?>

                            <option <?php echo $selected; ?> e value="<?php echo $player->ID; ?>">
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

	/**
	 * @param WP_Post $players
	 * @param WP_Post[] $current_players
	 *
	 * @return bool
	 */
	private static function IsCurrentPlayer( WP_Post $player, array $current_players ): bool {
		foreach ( $current_players as $current_player ) {
			if ( $player->ID === $current_player->ID ) {
				return true;
			}
		}

		return false;
	}
}