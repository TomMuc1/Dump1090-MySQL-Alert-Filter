### Dump1090-MySQL-Alert-Filter

#### Background
This is a set of php scripts which write Dump1090-mutability data to a local MySQL database. It can send e-mail or push notifications for special events, such as all planes within a geofence you prescribe, or certain planes identified either by hex code or tail number. You can set how often the script looks for an alert condition and writes to the database. The geofenced location is specified by latitude, longitude, and altitude. Further filtering can be done by listing hex codes and/or tail numbers. You can globally switch to wildcard-search and use % as a wildcard for missing characters in hex- or flight-text-file. In addition you can set the hex/flight filter to operate only within lat/lon/alt limit or for all locations received.

![Alt text](screen.png?raw=true "Script running on RaspberryPi")

Below is an example of a one line sample database query using an inner join to basestation.sqb:

    select * from aircrafts inner join basestation on aircrafts.hex = basestation.ModeS where aircrafts.hex = '3ddc68'

    id          message_date                    now             hex     flight  distance    altitude    lat         lon         track   speed   vert_rate   seen_pos    seen    rssi    messages    category    squawk  nucp    mlat    tisb    rec_msg_sec AircraftID  ModeS   ModeSCountry    Country Registration    Status  Manufacturer        ICAOTypeCode    Type                    SerialNo    RegisteredOwners    OperatorFlagCode
    13520524    2017-11-22 12:12:57 Wednesday   1511349177.8    3ddc68  CHX87   24.3        2875        48.395966   11.484023   4       117     0           11.8        1.6     -22.2   2128        A7          0020    7                       1281.9      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520449    2017-11-22 12:12:43 Wednesday   1511349163.4    3ddc68  CHX87   24.2        2875        48.394363   11.483812   5       115     0           0.4         0.4     -21.8   2103        A7          0020    7                       1287        102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520444    2017-11-22 12:12:42 Wednesday   1511349162.4    3ddc68  CHX87   23.9        2900        48.389189   11.48317    5       115     0           9.1         0.6     -21.8   2102        A7          0020    7                       1285.6      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520399    2017-11-22 12:12:33 Wednesday   1511349153.1    3ddc68  CHX87   23.9        2900        48.388444   11.483098   5       115     0           1.2         0       -18.6   2087        A7          0020    7                       1299.8      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520389    2017-11-22 12:12:31 Wednesday   1511349151.1    3ddc68  CHX87   23.8        2900        48.387863   11.483037   6       114     64          0.2         0.2     -17.8   2078        A7          0020    7                       1299.4      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520384    2017-11-22 12:12:30 Wednesday   1511349150.1    3ddc68  CHX87   23.8        2900        48.38681    11.482826   6       114     64          1.3         0.9     -17.1   2076        A7          0020    7                       1296.2      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520374    2017-11-22 12:12:28 Wednesday   1511349148      3ddc68  CHX87   23.7        2900        48.385849   11.482685   6       115     64          1           0.3     -17.6   2070        A7          0020    7                       1310.3      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520364    2017-11-22 12:12:25 Wednesday   1511349145.9    3ddc68  CHX87   23.7        2875        48.385279   11.482592   6       115     64          0           0       -17.8   2064        A7          0020    7                       1312.3      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520359    2017-11-22 12:12:24 Wednesday   1511349144.9    3ddc68  CHX87   23.6        2875        48.384068   11.482448   6       115     64          1.3         0.7     -18.2   2057        A7          0020    7                       1314.2      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
    13520351    2017-11-22 12:12:22 Wednesday   1511349142.8    3ddc68  CHX87   23.6        2875        48.383102   11.482262   8       118     0           1           0.7     -18.6   2052        A7          0020    7                       1316.4      102138      3DDC68  Germany D       D-HDSI  A                       Airbus Helicopters  EC45            MBB-BK 117 D-2 (H145)   20056       HDM Luftrettung     EC45
                                                                                                                                                                                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                                                                                                                                                                                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                                                                                                                                                                               
#### Instructions
##### Edit the upper lines of the radar.php file

Items required to be entered include:

1. For email notifications, provide the maximum and minimum geographic coordinates and maximum altitude of interest. If your longitude is negative, use the correct magnitude, for instance, max=-121.0000 min=-110.0000. Enter the maximum altitude in feet. 
2. Provide similar information for the push notification option. 
3. Provide the default look-up interval in seconds.
4. Provide either your Gmail information, or if using webmail or pushover.net, see further instructions in mailer.php. No key is required for use of Gmail.
5. Set the parameters needed for your local database connection.
6. Set the paths to the aircraft.json, mailer.php, hex_code_array, and flight_code_array files
7. Set the logic for alerts.
8. Set the local time zone, see https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
   
Place the radar.php script in /home/pi/ 

##### Setup script system service:

    sudo chmod 755 /home/pi/radar.php
    sudo nano /etc/systemd/system/radar.service

    In nano insert the following lines:

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

    Save and exit nano using ctrl+x -> ctrl+y -> enter

    sudo chmod 644 /etc/systemd/system/radar.service
    sudo systemctl enable radar.service
    sudo systemctl start radar.service
    sudo systemctl status radar.service
    
##### Starting with raspbian jessie or stretch install with dump1090-mutability (see below for Raspbian Stretch):
    
    sudo apt-get update
    sudo apt-get install sendmail
    sudo apt-get install php5-common php5-cgi php5-mysql php5-sqlite php5-curl php5
    sudo lighty-enable-mod fastcgi
    sudo lighty-enable-mod fastcgi-php
    sudo service lighttpd force-reload
    sudo apt-get install mysql-server mysql-client
    
    sudo shutdown -r now
    
    restart the device
    
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

##### Run the script for some seconds/minutes until it says xxx inserted then stop script with ctrl + z
    
    mysql -u root -p
    
    USE adsb;
    
    SELECT * FROM aircrafts;
    
    exit
    
##### For Raspbian Stretch 
The php-install line is:
    sudo apt-get install php7.0-common php7.0-cgi php7.0-mysql php7.0-sqlite php7.0-curl php7.0
    
The new mariadb (aka mysql) that comes with stretch is somewhat stupid with the root password. these steps help to get back the old behavior:
    
    sudo mysql -u root -p (leave password empty)
    update mysql.user set password=password('YOUR_DB_PASSWORD') where user='root';
    update mysql.user set plugin='' where user='root';
    flush privileges;
