<?php 

// To render array 
include("kint/Kint.class.php");

// include geonames class
include("geonames_suite.php");

$db_params['host'] = "localhost";
$db_params['login'] = "root";
$db_params['password'] = "secret";
$db_params['database'] = "geonames";


// $format="json";
$format="php";

$geonames=new Geonames_Suite($db_params,$format);

// Find a city
$cities_result=$geonames->retrieveCityStartWith("pa","all","en",10);
Kint::dump($cities_result);


// Retrieve city details
$city_details=$geonames->retrieveInformations("3030300","en");
Kint::dump($city_details);


// Retrieve all cities of admin1 zone of a country
$cities_admin1_result=$geonames->retrieveAdmin1cities("b2","fr","fr");
Kint::dump($cities_admin1_result);

// Retrieve all cities of admin2 zone of a country
$cities_admin2_result=$geonames->retrieveAdmin2cities("b2","88","fr","fr");
Kint::dump($cities_admin2_result);

// retrieve all countries
$countries_list_result=$geonames->retrieveCountryList();
Kint::dump($countries_list_result);

// retrieve country details
$country_details_result=$geonames->retrieveCountryInformations("fr");
Kint::dump($country_details_result);

// retrieve all admin1 of a country
$admin1_result=$geonames->retrieveAdmin1("fr","en");
Kint::dump($admin1_result);

// retrieve all admin2 of a country
$admin2_result=$geonames->retrieveAdmin2("b2","fr","en");
Kint::dump($admin2_result);


?>