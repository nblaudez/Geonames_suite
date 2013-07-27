<?php 
class Geonames_Suite_Db_Admin1 {
	
	public $_db = null;
	
	public function __construct($db) {
		$this->_db=$db;
	}
	
	
	/**
	 * This methods permits to retrieves admin1 zones list for a country.
	 * 
	 * @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	 * @param $language_iso_code string ISO_ALPHA2 code of country of language used to make query (Ex: if you specifiy IT as language_iso_code you
	 * 						can find 'Paris' city with the keyword 'Parigi'.
	 *
	 * @return mixed list of admin1 zones
	 */
public function retrieveAdmin1($country_code,$language_iso_code="en") {
		

		if(!preg_match('#^[a-zA-Z]{2}$#',$country_code))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$language_iso_code))
			return false;
				
		$query="SELECT DISTINCT
				geoname.geonameid, 
				geoname.country,
				geoname.admin1,
    	        COALESCE
    	        (
					( 
						SELECT alternatename.alternateName FROM alternatename 
						WHERE					
             	 			alternatename.geonameid=geoname.geonameid
             	 			AND alternatename.isoLanguage='$language_iso_code'
             	 			ORDER BY alternatename.isPreferredName DESC
             	 			LIMIT 1
                  	)
                  	, 
                  	geoname.name
                ) AS name,
                
				geoname.population
				
				FROM geoname
				WHERE
					geoname.country='$country_code'					
					AND geoname.fcode like 'ADM1'
				ORDER by name					
		";

		
		$stmt=$this->_db->query($query);


		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
}