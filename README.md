# Dump1090-MySQL-Alert-Filter
a simple php script that makes use of Dump1090-mutability aircraft.json and filters/writes to MySQL

It writes Dump1090-mutability data to database and e-mail alerts on special events. you can set how often the script writes to database and looks for alert condition. you can specify the area (lat/lon/alt) to be observed and filter for special hex and/or flight numbers.

![Alt text](screen.png?raw=true "Script running on RaspberryPi")

*** 5 Minute Express Install HowTo ***

given a raspbian jessie install with dump1090-mutability and lighttpd on raspberry-pi:

sudo apt-get update

sudo apt-get install sendmail

sudo apt-get install php5-common php5-cgi php5-mysql php5-sqlite php5-curl php5

sudo lighty-enable-mod fastcgi

sudo lighty-enable-mod fastcgi-php

sudo service lighttpd force-reload

sudo apt-get install mysql-server mysql-client

sudo shutdown -r now

mysql -u root -p

CREATE DATABASE adsb;

USE adsb;

CREATE TABLE `aircrafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `now` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `hex` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `flight` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `altitude` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `lat` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `lon` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `track` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `speed` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `vert_rate` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `seen_pos` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `seen` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `rssi` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `messages` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `category` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `squawk` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `nucp` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `mlat` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  `tisb` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11750 DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

SHOW COLUMNS IN aircrafts;

ctrl + z

php radar.php

run the script for some seconds/minutes until it says xxx inserted then stop script with ctrl + z

mysql -u root -p

USE adsb;

SELECT * FROM aircrafts;

et voila :)
