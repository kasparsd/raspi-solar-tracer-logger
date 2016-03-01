<?php

// Send all errors to this file
ini_set( 'error_log', __FILE__ . '.log' );

// Looks for TracerComms in /usr/bin/TracerComms
// https://github.com/StereotypicalSquirrel/TracerComms
$readings = exec( 'TracerComms' );

if ( empty( $readings ) ) {
	error_log( 'Tracer failed to read TracerComms params.' );
	die;
}

$logwrite = file_put_contents(
	sprintf( __DIR__ . '/logs/%s.csv', date('Ymd') ),
	sprintf( "%s,%s\n", date('c'), $readings ),
	FILE_APPEND
);

if ( ! $logwrite ) {
	error_log( 'Tracer failed to log to CSV.' );
}

$keys = array(
	'batteryvoltage',
	'solarvoltage',
	'chargecurrent',
	'loadcurrent',
	'temperature',
	'loaddetected',
	'overloaded',
	'shortcircuit',
	'overcharged',
	'batterylow',
	'batteryfull',
	'charging',
);

$values = explode( ',', $readings );

if ( empty( $values ) || count( $values ) !== 12 ) {
	error_log( sprintf(
		'Tracer failed to upload data or it did not match. Received %d values [%s].',
		count( $values ),
		$values
	) );
	die;
}

$send = array_combine( $keys, $values );

$latest = array_merge(
	$send,
	array(
		'timestamp' => date('c'),
		'uname' => exec( 'uname -a' ),
	)
);

$latestwrite = file_put_contents(
	__DIR__ . '/logs/latest.json',
	json_encode( $latest, JSON_PRETTY_PRINT )
);

if ( ! $latestwrite ) {
	error_log( 'Tracer failed to log to latest JSON.' );
}
