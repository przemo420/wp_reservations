<?php if( !defined('ABSPATH') ) die();
/**
 * Plugin Name: Reservations
 * Description: Reservation System
 * Version: 1.0
 * Requires at least: 5.7
 * Requires PHP: 7.2
 */
 
require_once( 'class_reservations.php' );

$reservations = new Reservations();

$reservations->addBox( 'Date', 'resDate', 'date' );
$reservations->addBox( 'Name', 'resName', 'text' );
$reservations->addBox( 'Confirmed', 'resConfirm', 'checkbox' );