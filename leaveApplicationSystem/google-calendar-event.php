<?php

/**
 * Plugin Name: Google Calendar Event
 * Description: create custom google calendar events.
 * Version: 1.0.0
 * Author: Finegap
 * Author URI: https://finegap.com
 * Text Domain: Google Calendar
 */

// Exit if accessed directly
//defined('ABSPATH') || exit;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
ini_set('display_errors', 1);

/**
 *  update google code
 */
//add_action('wp_footer', 'create_google_calendar_events');

function create_google_calendar_events($applicationID, $conn)
{
	// Get application from database
	$db_query = $conn->prepare("SELECT e.employee_name, a.leaveType, a.startDate, a.endDate, a.remarks FROM employee AS e, application AS a WHERE e.employee_id = a.employeeID AND a.id=$applicationID");
	$db_query->execute();
	$db_query->bind_result($empName, $leaveType, $startDate, $endDate, $remarks);
	$db_query->fetch();
	$db_query->close();
	
    $credentials = __DIR__ . '/credentials.json';
    require __DIR__ . '/vendor/autoload.php';
    
    $client = new Google_Client();
    $client->setApplicationName('Application Event');
    $client->setScopes(array(Google_Service_Calendar::CALENDAR));
    $client->setAuthConfig($credentials);
    $client->setAccessType('offline');
    $client->getAccessToken();
    $client->getRefreshToken(); 

    $service = new Google_Service_Calendar($client);

	// This part of code is modified to store leave's information
    $event   = new Google_Service_Calendar_Event(array(
        'summary' => $empName . ' - ' . $leaveType,
		'location' => '',
        'description' => $remarks,
        'start' => array(
        'date' => $startDate,
        ),
        'end' => array(
        'date' => $endDate,
        ),
        'recurrence' => array(),
        'attendees' => array(),  
        'reminders' => array(),
    ));

	$calendarId = '89ada0166e4e105cf755b09267e8667a63582168898f7b403aeeaecc10fa134b@group.calendar.google.com';
	// Insert the event into specific calendar
	$event = $service->events->insert($calendarId, $event);
	
	// Get Google Calendar's event ID for future use
	$eventID = $event->id;
	
	// Save event ID in database
	$db_query = $conn->prepare("UPDATE application SET eventID='$eventID' WHERE id=$applicationID");
	$db_query->execute();
	$db_query->close();

}

function remove_google_calendar_events($applicationID, $conn)
{
	// Get application from database
	$db_query = $conn->prepare("SELECT eventID FROM application WHERE id=$applicationID");
	$db_query->execute();
	$db_query->bind_result($eventID);
	$db_query->fetch();
	$db_query->close();
	
    $credentials = __DIR__ . '/credentials.json';
    require __DIR__ . '/vendor/autoload.php';
    
    $client = new Google_Client();
    $client->setApplicationName('Application Event');
    $client->setScopes(array(Google_Service_Calendar::CALENDAR));
    $client->setAuthConfig($credentials);
    $client->setAccessType('offline');
    $client->getAccessToken();
    $client->getRefreshToken(); 

    $service = new Google_Service_Calendar($client);
	$calendarId = '89ada0166e4e105cf755b09267e8667a63582168898f7b403aeeaecc10fa134b@group.calendar.google.com';
	
	// Delete the event from the specific calendar
	$service->events->delete($calendarId, $eventID);
	
	// Clear event ID in database
	$db_query = $conn->prepare("UPDATE application SET eventID='' WHERE id=$applicationID");
	$db_query->execute();
	$db_query->close();

}