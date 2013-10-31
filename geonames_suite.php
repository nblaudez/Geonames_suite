<?php 

class Geonames_Suite {
	
	public $_db = null;
	public $_format;
	public $_methods = array();
	
	
	public function __construct($db_params,$format="php") {
		
		$this->_format=$format;
		
		$this->_db=new PDO('mysql:host='.$db_params['host'].';dbname='.$db_params['database'],$db_params['login'],$db_params['password']);
		$this->_db->exec("set names utf8");
		
		$classes = scandir(__DIR__.'/classes/Db');
		
		foreach($classes as $class) {
			
			if($class !="." && $class !="..") {
				
				include_once(__DIR__."/classes/Db/".$class);
				
				$class_name="Geonames_Suite_Db_".substr($class,0,strlen($class)-4);
				$this->_methods[$class_name]=get_class_methods($class_name);
				$this->_instances[$class_name]=new $class_name($this->_db);
			}				
					
		}
		
	}
	

	
	public function __call($func,$params) {
		
		foreach($this->_methods as $class=>$class_methods) {
			if(in_array($func,$class_methods)) {	
				switch($this->_format) {
					case "php":						
						return call_user_func_array(array($this->_instances[$class],$func),$params);
						break;
					case "json":
						return json_encode(call_user_func_array(array($this->_instances[$class],$func),$params));
						break;
				}	
			}		
		}
		
		return false;
	}

	/**
	 * This method permits to retrieve geonames place informations.
	 *
	 * @param $string sring characters used to search cities. City name must start by this characters
	 * @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	 * @param $language_iso_code string ISO_ALPHA2 code of country of language used to make query
	 * 		  		(Ex: if you specifiy 'it' as language_iso_code you can find 'Paris' city with the keyword 'Parigi'.
	 * 				Informations are returned in language of language_iso_code if alternatesnames found for this geonameid.
	 *
	 * @return mixed An array of geoname place informations.
	 */
	public function retrieveInformations($geonameid,$language_iso_code="en") {
	
		if(!preg_match('#^[0-9]{1,7}$#',$geonameid))
			return false;
		if(!preg_match('#^[a-zA-Z]{2}$#',$language_iso_code))
			return false;		
		
		$query="SELECT DISTINCT
		geoname.geonameid as geonameid,
			
		geoname.longitude,
		geoname.latitude,
		geoname.population,
			
		COALESCE(
			( 
				SELECT alternatename0.alternateName FROM alternatename as alternatename0
				WHERE
					alternatename0.geonameid=geoname.geonameid
					AND alternatename0.isoLanguage='$language_iso_code'
				ORDER BY alternatename0.isPreferredName = 1 DESC
				LIMIT 1
			)
			,
			geoname.name
		) AS name,
	
	
		countryinfo.geonameid as country_id,
		geoname.country AS country_code,
		COALESCE(
			( 
				SELECT alternatename1.alternateName FROM alternatename as alternatename1
				WHERE
					alternatename1.geonameid=countryinfo.geonameId
					AND alternatename1.isoLanguage='$language_iso_code'
				ORDER BY alternatename1.isPreferredName DESC
				LIMIT 1
			)
			,
			countryinfo.name
		) AS country_name,
			
		( 
			SELECT geoname1.geonameid
			FROM geoname AS geoname1
			WHERE geoname1.country =  geoname.country
				AND geoname1.admin1 =  geoname.admin1
				AND geoname1.fcode =  'ADM1'
			LIMIT 1
		 
		) as admin1_id,
	
		geoname.admin1 as admin1_code,
			
		(
			SELECT geoname1.geonameid
			FROM geoname AS geoname1
			WHERE geoname1.country =  geoname.country
				AND geoname1.admin1 =  geoname.admin1
				AND geoname1.fcode =  'ADM1'
			LIMIT 1
		) as admin1_id,
		 
		COALESCE(
			(
				SELECT alternatename2.alternateName
				FROM alternatename as alternatename2
				WHERE alternatename2.geonameid = 
					(
					SELECT geoname2.geonameid
					FROM geoname AS geoname2
					WHERE geoname2.country =  geoname.country
						AND geoname2.admin1 =  geoname.admin1
						AND geoname2.fcode =  'ADM1'
					LIMIT 1 
					)
					AND alternatename2.IsoLanguage =  '$language_iso_code'
				ORDER by alternatename2.isPreferredName DESC
				LIMIT 1
			)
			,
			(
				SELECT geoname3.name
				FROM geoname AS geoname3
				WHERE geoname3.country =  geoname.country
					AND geoname3.admin1 =  geoname.admin1
					AND geoname3.fcode =  'ADM2'
					AND geoname3.admin2 = geoname.admin2
				LIMIT 1 
			)
		) as admin1_name,
		 
		(
			SELECT geoname4.geonameid
			FROM geoname AS geoname4
			WHERE geoname4.country =  geoname.country
				AND geoname4.admin1 =  geoname.admin1
				AND geoname4.fcode =  'ADM2'
				AND geoname4.admin2 = geoname.admin2
			LIMIT 1
		) as admin2_id,
		 
		COALESCE(
			(
				SELECT alternatename3.alternateName
				FROM alternatename as alternatename3
				WHERE alternatename3.geonameid = 
					(
						SELECT geoname5.geonameid
						FROM geoname AS geoname5
						WHERE geoname5.country =  geoname.country
							AND geoname5.admin1 =  geoname.admin1
							AND geoname5.fcode =  'ADM2'
							AND geoname5.admin2 = geoname.admin2
						LIMIT 1 
					)
					AND alternatename3.IsoLanguage =  '$language_iso_code'
				ORDER by alternatename3.isPreferredName DESC
				LIMIT 1
			)
			,
			(
				SELECT geoname6.name
				FROM geoname AS geoname6
				WHERE geoname6.country =  geoname.country
					AND geoname6.admin1 =  geoname.admin1
					AND geoname6.fcode =  'ADM2'
					AND geoname6.admin2 = geoname.admin2
				LIMIT 1
			)
		) as admin2_name,
			
		geoname.admin2 as admin2_code
	
		FROM geoname,countryinfo
			
		WHERE geoname.geonameid='$geonameid'
		And countryinfo.iso_alpha2=geoname.country
		";
	
			
		$stmt=$this->_db->query($query);
	
	
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	}
	
}
?>