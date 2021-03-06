<?php 
class Geonames_Suite_Db_Admin2 {
	
	public $_db = null;
	
	public function __construct($db) {
		$this->_db=$db;
	}
	
	
	/**
	 * This method permits to retrieve admin1 zones list for a country.
	 * 
	 * @param $admin1_code string code of admin1 zone
	 * @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	 * @param $language_iso_code string ISO_ALPHA2 code of country of language used to make query (Ex: if you specifiy IT as language_iso_code you
	 * 						can find 'Paris' city with the keyword 'Parigi'.
	 *
	 * @return mixed list of admin2 zones of a admin1 zone of a country
	 */
public function retrieveAdmin2($admin1_code,$country_code,$language_iso_code="en") {
		
		$language_iso_code=strtolower($language_iso_code);
		$country_code=strtolower($country_code);
		$admin1_code=strtolower($admin1_code);
		
		if(!preg_match('#^[0-9a-z]{1,7}$#',$admin1_code))			
			return false;
		if(!preg_match('#^[a-z]{2}$#',$country_code))
			return false;
		if(!preg_match('#^[a-z]{2}$#',$language_iso_code))
			return false;
				
		

		
		$query="SELECT DISTINCT
				geoname.geonameid, 
				geoname.country,
				geoname.admin1,
				geoname.admin2,
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
				WHERE";
		
				if($country_code!="all")
					$query.=" geoname.country='$country_code'";
				if($country_code!="all" && $admin1_code!="all")					
					$query.=" AND geoname.admin1='$admin1_code'";
				elseif($country_code=="all" && $admin1_code!="all")
					$query.=" geoname.admin1='$admin1_code'";
					
				$query.=" AND geoname.fcode like 'ADM2'
				ORDER by name";
					
				
		$stmt=$this->_db->query($query);


		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
}