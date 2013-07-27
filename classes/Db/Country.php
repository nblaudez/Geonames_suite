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
	
	public function retrieveCountryList() {
	
		$query="SELECT DISTINCT
		countryinfo.iso_alpha2,
		countryinfo.iso_alpha3,
		countryinfo.name,
		countryinfo.capital
		FROM countryinfo
		";
	
		$stmt=$this->_db->query($query);
	
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	
}