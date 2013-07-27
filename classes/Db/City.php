<?php 

class Geonames_Suite_Db_City {
	
	public $_db = null;
	
	public function __construct($db) {
		$this->_db=$db;
	}
	
	
	/**
	 * This methods permits to retrieves cities which starts by characters passed by argument.
	 * 
	 * @param $string string characters used to search cities. City name must start by this characters
	 * @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	 * @param $language_iso_code string ISO_ALPHA2 code of country of language used to make query (Ex: if you specifiy IT as language_iso_code you 
	 * 						can find 'Paris' city with the keyword 'Parigi'. 
	 * @param $limit int number of returned results
	 * 
	 * @return mixed An array of cities informations.
	 */
	public function retrieveCityStartWith($string,$country_code="all",$language_iso_code="en",$limit=10) {
		
		if(strlen($string)<=0)
			return false;
		if(!preg_match('#^(all|[a-zA-Z]{2}){1}$#',$country_code))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$language_iso_code))
			return false;
		
		
		// Re-write country_code parameter to use it in query
		if($country_code=="all")
			$country_code="geoname.country";
		else
			$country_code="'$country_code'";
		
						
		// SQL Query
		$query="SELECT DISTINCT
					alternatename.geonameid
					FROM alternatename
						WHERE alternatename.alternateName like '$string%'
						AND alternatename.isoLanguage = '$language_iso_code'";		
		$stmt=$this->_db->query($query);
		$result=$stmt->fetchAll(PDO::FETCH_ASSOC);	
		$geonameid_string="";	
		foreach($result as $geonameid)
			$geonameid_string.=$geonameid['geonameid'].",";
		
		
		$query="SELECT DISTINCT
					geoname.geonameid
					FROM geoname
					WHERE geoname.name like '$string%'
					AND geoname.country='$country_code'
             	 	AND geoname.fcode like 'PP%' ";
		$stmt=$this->_db->query($query);
		$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $geonameid)
			$geonameid_string.=$geonameid['geonameid'].",";		
		
		
		$geonameid_string=substr($geonameid_string,0,strlen($geonameid_string)-1);		
		
		$query="SELECT DISTINCT
					geoname.geonameid AS geonameid, 
	    	        COALESCE(
						( SELECT alternatename.alternateName FROM alternatename 
							WHERE					
	             	 			alternatename.geonameid=geoname.geonameid
	             	 			AND alternatename.isoLanguage='$language_iso_code'
	             	 			ORDER BY alternatename.isPreferredName DESC
	             	 			LIMIT 1
	                  	)
	                  	, 
	                  	geoname.name
	                 ) AS name,
	                 
	                 
	             	 geoname.country AS country_code,
	
	                 COALESCE(
						( SELECT alternatename.alternateName FROM alternatename,admin1CodesAscii 
							WHERE					
								admin1CodesAscii.code=CONCAT(CONCAT(geoname.country,'.'),geoname.admin1)
	             	 			AND alternatename.geonameid=admin1CodesAscii.geonameid
	             	 			AND alternatename.isoLanguage='$language_iso_code'
								ORDER by alternatename.isPreferredName DESC
								LIMIT 1		
	                  	)
	                  	, 
	                  	(
	                  	SELECT admin1CodesAscii.name FROM admin1CodesAscii
							WHERE					
								admin1CodesAscii.code=CONCAT(CONCAT(geoname.country,'.'),geoname.admin1)             	 			
	                  	)
	                 ) as admin1
	                 
	                 FROM geoname
	                 
	             	 WHERE  
	             	 
	             	 geoname.geonameid IN ($geonameid_string)	
	             	 AND geoname.fcode like 'PP%'
	             	 AND geoname.country=$country_code
	             	 								             	              	 						 
					 ORDER BY geoname.population DESC LIMIT $limit";

		$stmt=$this->_db->query($query);
		
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * This methods permits to retrieves cities of ADM1 zone.
	 *
	 * @param $admin1_code string code of admin1 zone
	 * @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	 * @param $language_iso_code string ISO_ALPHA2 code of country of language used to make query (Ex: if you specifiy IT as language_iso_code you
	 * 						can find 'Paris' city with the keyword 'Parigi'.
	 *
	 * @return mixed An array of cities informations.
	 */
	public function retrieveAdmin1cities($admin1_code,$country_code,$language_iso_code="en") {
		
		if(!preg_match('#^[a-zA-Z0-9]{1,7}$#',$admin1_code))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$country_code))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$language_iso_code))
			return false;
				
		$query="SELECT DISTINCT
				geoname.geonameid AS geonameid, 
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
                ) AS name
		
				FROM geoname
				WHERE
					geoname.country='$country_code'
					AND geoname.admin1='$admin1_code'
					AND geoname.fcode like 'PP%'
				ORDER by name					
		";

		$stmt=$this->_db->query($query);


		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * This methods permits to retrieves cities of ADM2 zone.
	 *
	 * @param $admin1_code string code of admin1 zone
	 * @param $admin2_code string code of admin2 zone
	 * @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	 * @param $language_iso_code string ISO_ALPHA2 code of country of language used to make query (Ex: if you specifiy IT as language_iso_code you
	 * 						can find 'Paris' city with the keyword 'Parigi'.
	 *
	 * @return mixed An array of cities informations.
	 */
	public function retrieveAdmin2cities($admin1_code,$admin2_code,$country_code,$language_iso_code="en") {
	

		if(!preg_match('#[a-zA-Z0-9]{1,7}#',$admin1_code))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$country_code))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$language_iso_code))
			return false;		
		
		$query="SELECT DISTINCT
				geoname.geonameid AS geonameid,
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
				) AS name
	
				FROM geoname
				WHERE
					geoname.country='$country_code'
					AND geoname.admin1='$admin1_code'
					AND geoname.admin2='$admin2_code'
					AND geoname.fcode like 'PP%'
				ORDER by name
		";
	
		$stmt=$this->_db->query($query);
	
	
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	

}