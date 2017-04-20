<?php

$basePath = getenv( 'MW_INSTALL_PATH' ) !== false ? getenv( 'MW_INSTALL_PATH' ) : __DIR__ . '/../../..';

require_once $basePath . '/maintenance/Maintenance.php';

class OpauthProfileMaintenance extends \Maintenance {

	public function execute() {
		//TODO: Implement execute() method.
		$dbw = wfGetDB(DB_SLAVE);
		$results = $dbw->select(
			'user',
			'user_id'
		);

		while( $row = $results->fetchRow() ) {
			print ".";
			if( !OpauthProfile::exists( $row['user_id'] ) ) {
				print "!";
				$profile = new OpauthProfile( $row['user_id'] );
				$profile->provider = 'local';
				$profile->save();
			}
		}
	}

}

$maintClass = 'OpauthProfileMaintenance';
require_once ( RUN_MAINTENANCE_IF_MAIN );
