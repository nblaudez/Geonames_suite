<?php 

class Geonames_Suite_Db_Country {
	
	public $_db=null;
	
	
	public function __construct($db) {
		$this->_db=$db;
	}
	
	/**
	*	This method permits to retrieve country informations
	*
	*   @param $country_code string ISO_ALPHA2 code of country of city. indicate 'all' for search in all countries
	*	@param string  iso_alpha2 code of country 
	*/

	public function retrieveCountryInformations($country_code) {

		if(!preg_match('#^[a-zA-Z]{2}$#',$country_code))
			return false;
		
		$query="SELECT DISTINCT
					countryinfo.iso_alpha2,
					countryinfo.iso_alpha3,
					countryinfo.iso_numeric,
					countryinfo.fips_code,
					countryinfo.name,
					countryinfo.capital,
					countryinfo.areainsqkm,
					countryinfo.population,
					countryinfo.continent,
					countryinfo.tld,
					countryinfo.continent,
					countryinfo.currency,
					countryinfo.currencyName,
					countryinfo.phone,
					countryinfo.postalCodeFormat,
					countryinfo.postalCodeRegex,
					countryinfo.geonameid,
					countryinfo.languages,
					countryinfo.neighbours,
					countryinfo.equivalentFipsCode					
				
				FROM countryinfo
				
				WHERE
					countryinfo.iso_alpha2='$country_code';
		";
				
		$stmt=$this->_db->query($query);
				
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}
	
	/**
	 *	This method permits to retrieve list of countries
	 *	 
	 */
	
	public function retrieveCountryList($language_iso_code="gb") {
	
		$query="SELECT 
                    DISTINCT countryinfo.iso_alpha2, 
                    countryinfo.iso_alpha3, 
                    COALESCE( 
                        ( SELECT alternatename.alternateName FROM alternatename
                            WHERE alternatename.geonameid=countryinfo.geonameId 
                            AND alternatename.isoLanguage='$language_iso_code' 
                            ORDER by alternatename.isPreferredName 
                            DESC LIMIT 1	 
                        ) , 
                        (SELECT countryinfo.name 
                            FROM countryinfo ci2 
                            WHERE ci2.geonameId=countryinfo.geonameId LIMIT 1
                        ) 
                    ) 
                    as name, 
                    countryinfo.capital 
                    FROM countryinfo 
		";
		$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$this->_db->query($query);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	
}