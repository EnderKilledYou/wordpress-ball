<?php

class BallAdminNoticeHandler {
	public static function AddError( $log ): void {
		$error   = self::get_admin_errors();
		$error[] = $log;
		self::updateAdminErrors( $error );
	}

	public static function AddNotice( $log ): void {
		$errors   = self::get_admin_notices();
		$errors[] = $log;
		self::updateAdminNotices( $errors );
	}

	public static function BallAdminNotice(): void {
		$errors  = self::get_admin_errors();
		$notices = self::get_admin_notices();
		self::printAdminNotices( $notices );
		self::printAdminErrors( $errors );
		self::clearAdminNotices();
		self::clearAdminErrors();
	}

	/**
	 * @return array
	 */
	public static function get_admin_errors(): array {
		$e = get_option( "admin_error" );
		if ( ! isset( $e ) || ! $e ) {
			$errors = [];
		} else {
			try {
				$errors = json_decode( $e, true, 512, JSON_THROW_ON_ERROR );
			} catch ( JsonException $e ) {
				$errors = [];
			}
		}

		return $errors;
	}

	/**
	 * @return array
	 */
	public static function get_admin_notices(): array {
		$e = get_option( "admin_notice" );
		if ( ! isset( $e ) || ! $e ) {
			$errors = [];
		} else {
			try {
				$errors = json_decode( $e, true, 512, JSON_THROW_ON_ERROR );
			} catch ( JsonException $e ) {
				$errors = [];
			}
		}

		return $errors;
	}

	/**
	 * @param array $errors
	 *
	 * @return void
	 */
	public static function updateAdminNotices( array $errors ): void {
		try {
			update_option( 'admin_notice', json_encode( $errors, JSON_THROW_ON_ERROR ) );
		} catch ( JsonException $e ) {
			echo "could not set admin_notice";
			echo $e->getMessage();
		}
	}
	/**
	 * @param array $error
	 *
	 * @return void
	 */
	public static function clearAdminNotices(): void {

		update_option( 'admin_notice', '[]' );

	}
	/**
	 * @param array $error
	 *
	 * @return void
	 */
	public static function clearAdminErrors(): void {

		update_option( 'admin_error', '[]' );

	}

	/**
	 * @param array $error
	 *
	 * @return void
	 */
	public static function updateAdminErrors( array $error ): void {
		try {
			update_option( 'admin_error', json_encode( $error, JSON_THROW_ON_ERROR ) );
		} catch ( JsonException $e ) {
			echo "could not set admin_error ";
			echo $e->getMessage();
		}
	}

	/**
	 * @param array $errors
	 *
	 * @return void
	 */
	public static function printAdminErrors( array $errors ): void {
		foreach ( $errors as $error ) {
			?>

            <div class="notice notice-error is-dismissible">
				<?php
				echo wp_strip_all_tags( $error );

				?>
            </div>
			<?php

		}
	}

	/**
	 * @param array $notices
	 *
	 * @return void
	 */
	public static function printAdminNotices( array $notices ): void {
		foreach ( $notices as $notice ) {
			?>
            <div class="notice notice-success is-dismissible">
				<?php
				echo wp_strip_all_tags( $notice );
				?>
            </div>
			<?php
		}
	}
}