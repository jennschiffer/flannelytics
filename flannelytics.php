<?php
/*
  Plugin Name: Flannelytics
  Description: Connects to the Google Analytics API to gets page view data
  Author: Jenn Schiffer
  Version: 1.0
  Author URI: http://jennmoney.biz
  Github: http://github.com/jennschiffer/flannelytics
*/

// require config
require_once 'config.php';

if ( !$key_file_location || !$service_account_email || !$start_date || !$end_date ) {
  die('you must enter all values in config.php for this to work wowowowow');
}

// create service object
function getService() {
  require_once 'google-api-php-client/src/Google/autoload.php';

  $client = new Google_Client();
  $client->setApplicationName("Metafluanalytics");
  $analytics = new Google_Service_Analytics($client);

  global $key_file_location, $service_account_email;
  $key = file_get_contents($key_file_location);

  $cred = new Google_Auth_AssertionCredentials(
      $service_account_email,
      array(Google_Service_Analytics::ANALYTICS_READONLY),
      $key
  );

  $client->setAssertionCredentials($cred);

  if( $client->getAuth()->isAccessTokenExpired() ) {
    $client->getAuth()->refreshTokenWithAssertion($cred);
  }

  return $analytics;
}

// get view profile id
function getFirstprofileId( &$analytics) {
  $accounts = $analytics->management_accounts->listManagementAccounts();

  if ( count( $accounts->getItems() ) > 0 ) {
    $items = $accounts->getItems();
    $firstAccountId = $items[0]->getId();

    $properties = $analytics->management_webproperties->listManagementWebproperties( $firstAccountId );

    if ( count( $properties->getItems() ) > 0 ) {
      $items = $properties->getItems();
      $firstPropertyId = $items[0]->getId();

      $profiles = $analytics->management_profiles->listManagementProfiles( $firstAccountId, $firstPropertyId );

      if ( count( $profiles->getItems() ) > 0 ) {
        $items = $profiles->getItems();
        return $items[0]->getId();
      }
      else {
        throw new Exception( 'No views (profiles) found for this user.' );
      }
    }
    else {
      throw new Exception( 'No properties found for this user.' );
    }
  }
  else {
    throw new Exception( 'No accounts found for this user.' );
  }
}

// get number of sessions for last seven days
function getResults( &$analytics, $profileId, $slug ) {
  global $start_date, $end_date;

  $optParams = array(
    'dimensions' => 'ga:pagePath',
    'metrics' => 'ga:pageviews',
    'filters' => 'ga:pagePath==' . $slug,
    'max-results' => 1
  );

  $results = $analytics->data_ga->get(
      'ga:' . $profileId,
      $start_date,
      $end_date,
      'ga:sessions',
      $optParams
  );

  return $results;
}

// get views of a specific page and return
function getPageViews( $slug ) {
  global $analytics, $profile;

  $results = getResults( $analytics, $profile, $slug );

  if ( count( $results->getRows() ) < 1 ) {
    return;
  }

  $profileName = $results->getProfileInfo()->getProfileName();
  $rows = $results->getRows();
  $views = $rows[0][1];

  return $views;
}

$analytics = getService();
$profile = getFirstProfileId( $analytics );
