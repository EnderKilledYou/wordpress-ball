<?php

class BallAdminNoticeHandler {
	public static function BallAdminNotice() {
		if ( $_SESSION['admin_notice'] ) {
			?>

            <div class="notice notice-success is-dismissible">
				<?php
				echo wp_strip_all_tags( $_REQUEST['admin_notice'] );
				unset( $_SESSION['admin_notice'] );
				?>
            </div>
			<?php

		}
		if ( $_SESSION['admin_error'] ) {
			?>

            <div class="notice notice-error is-dismissible">
				<?php
				echo wp_strip_all_tags( $_REQUEST['admin_error'] );
				unset( $_SESSION['admin_error'] );
				?>
            </div>
			<?php

		}

	}
}