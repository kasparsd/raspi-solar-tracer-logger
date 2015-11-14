<?php
// Convert CSV output of TracerComms into a JSON blob

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

$t = exec( 'TracerComms' );
$t = array_map( 'floatval', explode( ',', $t ) );

header( 'Content-Type: application/json' );

echo json_encode( array(
	'error' => false,
	'result' => array_combine( $keys, $t )
) );
