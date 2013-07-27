Geonames suite
---------------

Geonames_suite is PHP library which permits to manipulate geoname database.

At this instant geoname_suite permits to :

  - Retrieve localized city informations by different ways :

    - Cities of specific admin1 zone
    - Cities of specific admin2 zone
    - Cities whose name starts by a specific string in specific country

  - Retrieve localized admin1 informations for a country 

  - Retrieve localized admin2 informations for a country

  - Retrieve country information

Installation
=============

Geoname database
-------------

You need to set up a geoname database on a Mysql server.
In this way i suggest you to use "GeoNames-MySQL-DataImport" a git project which download and set up the geoname database.

You can clone it here : https://github.com/codigofuerte/GeoNames-MySQL-DataImport

The next step is to create Indexes on somes field to increase the execution time of SQL queries :

    ALTER TABLE `admin1CodesAscii` ADD INDEX `code` USING BTREE(`code`);
    ALTER TABLE `alternatename` ADD INDEX `alternateName` USING BTREE(`alternateName`);
    ALTER TABLE `alternatename` ADD INDEX `alternateName_isoLanguage` USING BTREE(`isoLanguage`, `alternateName`);
    ALTER TABLE `alternatename` ADD INDEX `geonameid` USING BTREE(`geonameid`);
    ALTER TABLE `alternatename` ADD INDEX `isoLanguage_2` USING BTREE(`isoLanguage`);
    ALTER TABLE `countryinfo` ADD INDEX `iso_alpha2` USING BTREE(`iso_alpha2`);
    ALTER TABLE `geoname` ADD INDEX `alternatenames` USING BTREE(`alternatenames`);
    ALTER TABLE `geoname` ADD INDEX `fcode` USING BTREE(`fcode`, `country`, `admin1`);
    ALTER TABLE `geoname` ADD INDEX `fcode_2` USING BTREE(`fcode`, `country`, `admin1`, `admin2`);
    ALTER TABLE `geoname` ADD INDEX `name` USING BTREE(`name`);
    
 

Utilisation
------------

see demo.php for examples


Credits
---------

Nicolas Blaudez  <a href='http://www.developpeur-php-independant.com'>PHP developper</a>
