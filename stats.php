<?php
// Build some stats from logs

$kwh = array();
$days = array();
$month = array();
$extra = array();

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

$logs = glob( __DIR__ . '/logs/*.csv' );

foreach ( $logs as $log ) {

	$fh = fopen( $log, 'r' );

	while ( ( $data = fgetcsv($fh) ) !== FALSE ) {

		if ( ! is_array( $data ) || ! isset( $data[3] ) )
			continue;

		$time = strtotime( $data[0] );
		$day = date( 'Y-m-d', $time );
		$m = date( 'Y-m', $time );
		$power = $data[1] * $data[3];

		if ( ! isset( $kwh[$day] ) )
			$kwh[$day] = array();

		end( $kwh[$day] );

		if ( key( $kwh[$day] ) )
			$t = $time - key( $kwh[$day] );
		else
			$t = 0;

		// Make sure this is no more than 60 seconds
		$t = min( 60, $t );

		$energy = $power * $t;

		$extra[ $day ][ 'timestamp' ][] = $time;

		$row = array_map( 'floatval', array_slice( $data, 1 ) );

		foreach ( $keys as $l => $label )
			$extra[ $day ][ $label ][] = $row[ $l ];

		$month[ $m ][ $time ] = $energy;
		$kwh[ $day ][ $time ] = $energy;

	}

}

$t = array();
$m = array();

foreach ( $month as $_m => $days ) {
	$m[ $_m ] = round( array_sum( $days ) / 3600, 1 );
}

foreach ( $kwh as $day => $power ) {
	$total = round( array_sum( $power ) / 3600, 1 );

	$t[ $day ] = $total;

	$log = array(
		'updated' => date('r'),
		'date' => $day,
		'total_Wh' => $total,
		'log' => $extra[ $day ],
	);

	file_put_contents(
		__DIR__ . '/logs/' . str_replace( '-', '', $day ) . '.json',
		json_encode( $log, JSON_PRETTY_PRINT )
	);
}

ksort($t);
ksort($m);

$r = array(
	'updated' => date('r'),
	'total_Wh' => array_sum( $t ),
	'daily_Wh' => $t,
	'montly_Wh' => $m
);

file_put_contents(
	__DIR__ . '/logs/totals.json',
	json_encode( $r, JSON_PRETTY_PRINT )
);
