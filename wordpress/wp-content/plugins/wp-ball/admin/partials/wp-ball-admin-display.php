<?php
include_once( "form_handler.php" );
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://goodcode.shop
 * @since      1.0.0
 *
 * @package    Wp_Ball
 * @subpackage Wp_Ball/admin/partials
 */
$seasons = PlayerHelper::get_seasons();
?>
<form method="post">
    <div class="form-group">
        <label>
            Add Test users and players

            <input type="submit" value="Add Test Users and players" name="add_users">
        </label>
    </div>
    <div class="form-group">
        <label>
            Add Test season with all players

            <input type="submit" value="Add Test Season" name="add_season" disabled>
        </label>
    </div>
    <div class="form-group">
        <label>
            Fill in a season with test scores.
            <select name="season_id">

				<?php
				foreach ( $seasons as $season ) { ?>
                    <option value="<?php echo $season->ID; ?>"> <?php echo $season->post_title; ?> </option>
				<?php } ?>
            </select>
            <input type="submit" value="Add Scores to Last season" name="add_scores"  >
        </label>
    </div>
</form>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
