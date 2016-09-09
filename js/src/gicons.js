/*
'grocery'			=> 'Grocery Stores',
	'restaurants'		=> 'Restaurants',
	'banks'				=> 'Banks',
	'gas_stations'	=> 'Gas Stations',
	'golf'				=> 'Golf Cources',
	'hospitals'			=> 'Hospitals',

*/
var iconSchools;
iconSchools = new GIcon(); 
iconSchools.title = 'Schools';
iconSchools.image = 'http://labs.google.com/ridefinder/images/mm_20_brown.png';
iconSchools.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconSchools.iconSize = new GSize(12, 20);
iconSchools.shadowSize = new GSize(22, 20);
iconSchools.iconAnchor = new GPoint(6, 20);
iconSchools.infoWindowAnchor = new GPoint(5, 1);

var iconGrocery;
iconGrocery = new GIcon(); 
iconGrocery.title = 'Grocery Stores';
iconGrocery.image = 'http://labs.google.com/ridefinder/images/mm_20_yellow.png';
iconGrocery.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconGrocery.iconSize = new GSize(12, 20);
iconGrocery.shadowSize = new GSize(22, 20);
iconGrocery.iconAnchor = new GPoint(6, 20);
iconGrocery.infoWindowAnchor = new GPoint(5, 1);

var iconRestaurants;
iconRestaurants = new GIcon(); 
iconRestaurants.title = 'Restaurants';
iconRestaurants.image = 'http://labs.google.com/ridefinder/images/mm_20_purple.png';
iconRestaurants.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconRestaurants.iconSize = new GSize(12, 20);
iconRestaurants.shadowSize = new GSize(22, 20);
iconRestaurants.iconAnchor = new GPoint(6, 20);
iconRestaurants.infoWindowAnchor = new GPoint(5, 1);

var iconHospitals;
iconHospitals = new GIcon(); 
iconHospitals.title = 'Hospitals';
iconHospitals.image = 'http://labs.google.com/ridefinder/images/mm_20_blue.png';
iconHospitals.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconHospitals.iconSize = new GSize(12, 20);
iconHospitals.shadowSize = new GSize(22, 20);
iconHospitals.iconAnchor = new GPoint(6, 20);
iconHospitals.infoWindowAnchor = new GPoint(5, 1);

var iconGolf;
iconGolf = new GIcon(); 
iconGolf.title = 'Golf Cources';
iconGolf.image = 'http://labs.google.com/ridefinder/images/mm_20_green.png';
iconGolf.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconGolf.iconSize = new GSize(12, 20);
iconGolf.shadowSize = new GSize(22, 20);
iconGolf.iconAnchor = new GPoint(6, 20);
iconGolf.infoWindowAnchor = new GPoint(5, 1);

var iconBanks;
iconBanks = new GIcon(); 
iconBanks.title = 'Banks';
iconBanks.image = 'http://labs.google.com/ridefinder/images/mm_20_white.png';
iconBanks.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconBanks.iconSize = new GSize(12, 20);
iconBanks.shadowSize = new GSize(22, 20);
iconBanks.iconAnchor = new GPoint(6, 20);
iconBanks.infoWindowAnchor = new GPoint(5, 1);

var iconGasStations;
iconGasStations = new GIcon(); 
iconGasStations.title = 'Gas Stations';
iconGasStations.image = 'http://labs.google.com/ridefinder/images/mm_20_gray.png';
iconGasStations.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
iconGasStations.iconSize = new GSize(12, 20);
iconGasStations.shadowSize = new GSize(22, 20);
iconGasStations.iconAnchor = new GPoint(6, 20);
iconGasStations.infoWindowAnchor = new GPoint(5, 1);

var custom_icons = new Array();
custom_icons['schools'] = iconSchools;
custom_icons['education'] = iconSchools;
custom_icons['grocery'] = iconGrocery;
custom_icons['restaurants'] = iconRestaurants;
custom_icons['hospitals'] = iconHospitals;
custom_icons['golf'] = iconGolf;
custom_icons['banks'] = iconBanks;
custom_icons['gas_stations'] = iconGasStations;