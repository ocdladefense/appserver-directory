# Appserver CAR Module

## Installation
### Summary
Installation consists of a PHP executable component. 

### PHP Executable
1.  Clone this repository into your local Appserver's /modules directory.
2.  Rename the newly-downloaded repository directory to carscraper/.


## Example endpoints - parsing project

### insert car urls for a set number of days, starting at a given date.
### use year, month, day to set initial date
### No prevailing "0" on month number and day number.
http://localhost/car/insert/urls?days=10&year=2021&month=5&day=5



### Insert case reviews using the urls from the validurls table.
http://localhost/car/scrape

### Insert case reviews for a specific date
http://localhost/car/scrape/day/09/30/2020

### Insert products into salesforce
http://localhost/<sitename>/insert


