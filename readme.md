# UCF-CREOL SQL JSON Reader/Formatter #

Provides a shortcode to display information from CREOL's current database. 


## Description ##

Provides a shortcode CREOL's SQL database, allows the collection of information from CREOL's database to the new
wordpress site.  


## Installation ##

### Manual Installation ###
1. Upload the plugin files (unzipped) to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the "Plugins" screen in WordPress

### WP CLI Installation ###

N/A

## Changelog ##

### 0.4.7 ###
* new args
* new layout for non-image formats 

### 0.4.3 ###

* added generic plugin shortcode to list of shortcode. will deprecate others in future version.   

### 0.4.0 ###

* Added new shortcode for publications
* Modified shortcode for bio data

### 0.3.3 ###

* changed shortcode name to ucf-creol-people-directory.
* set default to GrpID=1 and base_uri to SqltoJson.aspx.
* reformatted image card block to house more data. 
* linked images to respective older people homepage. 

### 0.3 ###
* test push - making sure it works with the github uploader on Creol Cms Dev. 


## Upgrade Notice ##

n/a


## Installation Requirements ##

None


## Development & Contributing ##

N/A

## Custom Shortcode Arguments ##

Several shortcode arguments correspond to the query value that it is allocated for. For example: 
* [ucf-creol] will provide the default loadout
* [ucf-creol stored_procedure='WWWPublications'] will set the stored procedure to the publications json. 

Current custom argument list includes: 
* base_uri (required/hardcoded) - base uri of API in CREOL database
* stored_procedure (required) - specify the type of stored procedure from the CREOL database
* layout (optional) - layout for display
* typelist (optional) - type of publication
* year (optional) - year of publication
* peopleid (optional) - person id for publications
* page (optional) - deciedes the length of pages new feature
* pagesize (optional) - for publications sets the anmount of query values returned
* grpid (required for people) - from stored procedure specifies the group id for people group within CREOL
* debug (optional) - shows debug of results from api call

### Wishlist/TODOs ###
* Allow more functionality for various edge case solutions for the database. 
* Comments
* gulp this readme 
* get email functionality 