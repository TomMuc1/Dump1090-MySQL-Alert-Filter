### Dump1090-MySQL-Alert-Filter

Writes Dump1090-mutability data to MySql database and e-mail/pushover alerts on special events. you can set how often the script writes to database and looks for alert condition. you can specify the area (lat/lon/alt) to be observed and filter for special hex and/or flight numbers. You can globally switch to wildcard-search and then use % as wildcard for missing characters in hex- or flight-text-file. in addition you can set hex/flight filter to operate only within lat/lon/alt limit or within whole range of site

![Alt text](screen.png?raw=true "Script running on RaspberryPi")

**=> do the needed settings at top of radar.php - then place the script e.g. in /home/pi/ and follow below instructions**

**setup script system service:**

    sudo chmod 755 /home/pi/radar.php
    sudo nano /etc/systemd/system/radar.service

-> in nano insert the following lines

    [Unit]
    Description=radar.php
    
    [Service]
    ExecStart=/home/pi/radar.php
    Restart=always
    RestartSec=10
    StandardOutput=null
    StandardError=null
    
    [Install]
    WantedBy=multi-user.target

save and exit nano ctrl+x -> ctrl+y -> enter

    sudo chmod 644 /etc/systemd/system/radar.service
    sudo systemctl enable radar.service
    sudo systemctl start radar.service
    sudo systemctl status radar.service
    
**starting with raspbian jessie install with dump1090 with lighttpd
    
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
      `message_date` varchar(100) DEFAULT NULL,
      `now` varchar(100) DEFAULT NULL,
      `hex` varchar(100) DEFAULT NULL,
      `flight` varchar(100) DEFAULT NULL,
      `distance` varchar(100) DEFAULT NULL,
      `altitude` varchar(100) DEFAULT NULL,
      `lat` varchar(100) DEFAULT NULL,
      `lon` varchar(100) DEFAULT NULL,
      `track` varchar(100) DEFAULT NULL,
      `speed` varchar(100) DEFAULT NULL,
      `vert_rate` varchar(100) DEFAULT NULL,
      `seen_pos` varchar(100) DEFAULT NULL,
      `seen` varchar(100) DEFAULT NULL,
      `rssi` varchar(100) DEFAULT NULL,
      `messages` varchar(100) DEFAULT NULL,
      `category` varchar(100) DEFAULT NULL,
      `squawk` varchar(100) DEFAULT NULL,
      `nucp` varchar(100) DEFAULT NULL,
      `mlat` varchar(100) DEFAULT NULL,
      `tisb` varchar(100) DEFAULT NULL,
      `rec_msg_sec` varchar(100) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
    
    SHOW COLUMNS IN aircrafts;
    
    exit
    
    php radar.php
    
    run the script for some seconds/minutes until it says xxx inserted then stop script with ctrl + z
    
    mysql -u root -p
    
    USE adsb;
    
    SELECT * FROM aircrafts;
    
    exit
    
    for raspbian stretch the php-install line is:
    sudo apt-get install php7.0-common php7.0-cgi php7.0-mysql php7.0-sqlite php7.0-curl php7.0
    
the new mariadb (aka mysql) that comes with stretch is somewhat stupid with the root password. these steps help to get back the old behavior:
    
    sudo mysql -u root -p (leave password empty)
    update mysql.user set password=password('YOUR_DB_PASSWORD') where user='root';
    update mysql.user set plugin='' where user='root';
    flush privileges;
