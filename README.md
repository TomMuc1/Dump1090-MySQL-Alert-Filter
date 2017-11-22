### Dump1090-MySQL-Alert-Filter

Writes Dump1090-mutability data to MySql database and e-mail/pushover alerts on special events. you can set how often the script writes to database and looks for alert condition. you can specify the area (lat/lon/alt) to be observed and filter for special hex and/or flight numbers. You can globally switch to wildcard-search and then use % as wildcard for missing characters in hex- or flight-text-file. in addition you can set hex/flight filter to operate only within lat/lon/alt limit or within whole range of site

![Alt text](screen.png?raw=true "Script running on RaspberryPi")

    id	        message_date	            now	            hex	    flight	distance	altitude	lat	        lon	        track	speed	vert_rate	seen_pos	seen	rssi	messages	category	squawk	nucp	mlat	                            tisb	rec_msg_sec	AircraftID	ModeS	ModeSCountry	Country	Registration	Status	Manufacturer	ICAOTypeCode	Type	        SerialNo	RegisteredOwners	OperatorFlagCode
    10638692	2017-10-27 8:58:38 Friday	1509087518.5	3cd05d	GFD6	26.4	    6650	    48.31019	10.704803	140	    325	    -2304	    0.8	        0	    -5.1	12374		            4270	0	    lat lon track speed vert_rate		        1275.1	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638707	2017-10-27 8:58:43 Friday	1509087523.6	3cd05d	GFD6	26.1	    6550	    48.305814	10.710369	140	    323	    -2048	    0.6	        0.3	    -5.9	12419		            4270	0	    lat lon track speed vert_rate		        1277.4	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638728	2017-10-27 8:58:51 Friday	1509087531.8	3cd05d	GFD6	25.3	    6375	    48.296085	10.722729	140	    320	    -1792	    0.9	        0	    -5.8	12505		            4270	0	    lat lon track speed vert_rate		        1283.2	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638746	2017-10-27 8:58:56 Friday	1509087536.9	3cd05d	GFD6	24.8	    6250	    48.288916	10.73133	140	    314	    -1600	    0	        0	    -6.9	12556		            4270	0	    lat lon track speed vert_rate		        1282.7	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638814	2017-10-27 8:59:14 Friday	1509087554.2	3cd05d	GFD6	23.8	    5775	    48.272715	10.745135	144	    298	    -1344	    0.4	        0	    -6.4	12672		            4270	0	    lat lon track speed vert_rate		        1300.3	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638845	2017-10-27 8:59:23 Friday	1509087563.4	3cd05d	GFD6	23.3	    5475	    48.263219	10.750628	147	    283	    -1472	    0.8	        0.3	    -5.4	12729		            4270	0	    lat lon track speed vert_rate		        1295.7	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638872	2017-10-27 8:59:32 Friday	1509087572.6	3cd05d	GFD6	22.7	    5175	    48.248601	10.751134	156	    276	    -1664	    0.2	        0	    -11.1	12781		            4270	0	    lat lon track speed vert_rate		        1288	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638893	2017-10-27 8:59:39 Friday	1509087579.8	3cd05d	GFD6	23.4	    4925	    48.242689	10.725909	182	    233	    -1728	    0.5	        0	    -11.5	12821		            4270	0	    lat lon track speed vert_rate		        1291	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
    10638911	2017-10-27 8:59:45 Friday	1509087585.9	3cd05d	GFD6	23.2	    4800	    48.23822	10.72562	182	    226	    -1856	    0.3	        0	    -5.4	12860		            4270	0	    lat lon track speed vert_rate		        1298	    28265	    3CD05D	Germany	D	    D-CGFD	A	                    Gates	        LJ35	        Learjet 35 A	35A-139	    GFD	                GFD
                                                                                                                                                                                                                                                                                                                                                                                                                                        

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
    
**starting with raspbian jessie or stretch install with dump1090-mutability:**
    
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
