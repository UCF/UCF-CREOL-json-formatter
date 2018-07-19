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

### Wishlist/TODOs ###
* Allow more functionality for various edge case solutions for the database. 
* Comments
* gulp this readme 
* get email functionality 