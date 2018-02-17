#			                               					
# Introduction	         			
#



Hello everyone. I have implemented parcel tracking system witch holds exact sender and
receiver addresses (Implicit in coordinate form), creation date (in modified timestamp)
and parcel type. Due to it is a lot information - we need a lot of symbols to code it.
Most of my project work is based on that problem. I tried to find the most efficient way to
lossy compress this information in tracking code.


0. Instalation shipment and deployment
1. Project structure for most important files
2. Dependency and  Technologies
3. Tracing Code Functionality
4. Persistent and Database



#
# 0. Instalation shipment and deployment
#


1. Clone project
```
git clone git@github.com:grambas/hermes-competition.git
```
2. Enter to project root folder and then start docker with command
```
docker-composer up -d
```
2. Junst in cacha clear the cache and assign right permissions
```
docker-compose exec php php artisan cache:clear
docker-compose exec php chmod -R 777 vendor storage
```
3. Migrate and seed database
```
docker-compose exec php php artisan migrate --seed
```
4. Visit http:/{ip}:8000/ or http:/localhost:8000/ . PhpMyAdmin at http:/localhost:8080 (login/pass - dev)


#
#                   1. Project structure for most important files
#

```
competition
|___app
|	 |___Http
|	 |___Controllers
|	 |		MainController.php	- Views,actions controller
|	 |
|	 |___Library
|	 |	    |___Hermes.php 		- Main class for compressing, reversing tracking code
|			|___Coordinate.php 	- Simple coordinate class adjusted only for this project
|	 | 
|	 |___Parcel.php 			- Database tables binding (Parcel,Status,Type)
|	 |___Status.php
|	 |___Type.php
|
|___database
|		|
|		|___migrations    		-   Automatically  generates tables for project
|		|		|
|		|		|___2018_02_12_1_create_parcels_table.php
|		|		|___2018_02_12_2_create_statuses_table.php
|		|		|___2018_02_12_3_create_types_table.php
|		|		|___2018_02_12_4_create_parcel_status_table.php
|		|
|		|___seeds 				- Fill important data to migrated tables
|		|	  |
|		|	  |___StatusTableSeeder.php
|		|	  |___TypesTableSeeder.php
|		|
|		|___public
|			  |___css
|			  |	   |
|			  |	   |___main.css
|			  |
|			  |___js
|				   |___main.js
|
|___resources - - Html files
|		|___view  
|		|___layout.blade.php
|		|___home.blade.php
|		|___simulation.blade.php
|		|___track.blade.php
|
|___routes   					-  URL structure and routing
|	   |___web.php
|
|___tests 						- PHPUnit tests
|	   |
|	   |___Feature
|			  |___HermesTest.php
.env
```


#
#                        2. Dependency and  Technologies
#


* Backend
  *  PHP Laravel Framework (PHP 7, nginx PHP-FPM)
  *  Mysql Database
  *  PHPUnit test tool


* Frontend
  *  Javsacript JQuery
  *  HTML bootstrap 4 framework
  
* Deployment
  *  Docker
* Other
  *  phpMyAdmin


#
# 3. Tracing Code Functionality
#

Contents information - exact sender and receiver coordinates, timestamp, and parcel type.

Length -  Depends on compression algorithm  and coding table. Max - 37, Min - 21

Compression algorithm  is implemented in both PHP and Javascript. For demonstration is used
PHP version (app/Library/Hermes.php)



Compression length statistic
============================


### Without compression (CONSTANT)

```
18 = 2*9 = Sender coordinates (latitude/longitude) xx.xxxxxx,xx.xxxxxx, (6decimal precision +dot)
+
18		 = Same for receiver
+
10       = Timestamp
+
2 		 = Parcel types (~20 types)
=
48 charts to save information
```

### With Compression (WORST CASE):

```
14 = 2*7 = Sender coordinates (latitude/longitude) x.xxxxxx,x.xxxxxx, (6decimal precision)
+
14		 = Same for receiver
+
8       = Timestamp
+
1 		 =Parcel types (~20 types)
=
37 chars to save information
```

### With Compression (BEST CASE):

```
8 = 2*4 = Sender coordinates (latitude/longitude) x.xxx,x.xxx, (6decimal precision)
+
8		 = Same for receiver
+
4       = Timestamp
+
1 		 =Parcel types (~20 types)
=
21 chars to save information
```

### Compression rate

Best Case  - 56.25 %

Worst Case - 22.92 %


## 2 Compression algorithm Overview


For compression are used letters. Two digits numbers will be replaced with
corresponding letter with is defined in class. Due to the fact that  integer 
part of coordinate  in EU region are similar  for both latitude and longitude, 
we can code it only in one symbol (inspired by Huffman coding).

All coordinate integer parts are paired  with letter. For example
-9 = A, 50 = B ... In this way we code 2 charts in one letter.


* Pros
  *  compression rate  
  *  readable  
  *  spellable code
  *  could be hashed (for privacy)
  
* Cons
  *  Alphabet is needed
  *  or actual version only EU coordinates
  *  not constant code size

# Compression algorithm  explain

### Compressing number

For all - coordinates,timestamp, and package type algorithm
search if 2 digit number is found in compression table if yes
two digits will be replaced by one letter.

### Compressing Coordinate

Coordinate format - NN.XXXXXX
All most frequented  NN variations  are defined in compress table.
If NN is not defined we add SEPERATOR between integer and decimal part.
Decimal part is coded like simple number.

### Timestamp
Originally timestamp has 10 digits. To reduce this size the algorithm 
subtracts  current timestamp from other constant time in seconds
For example we define one start timestamp (10 digits) witch is close to present. By creating tracking 
code we substracting this defined tiemstamp from current timestamp. It reduces 2 
digits in range for 3 years.
Example 1:
Defined start date  - 2018.01.01.     In unix timestmap - 1514764800
Lets say current date is 2018.02.01.  In unix timestmap - 1517443200
Current date - Start date = 2678400 => only 7 digits
Example 2
Start date    2018.01.01 (1514764800)
Current date  2021.01.01 (1609459200)
Current date - Start date = 9.46944 × 10^7 => only 8 digits

Thats proves that for 3 years we reduce 2 digits from timestamp.Even more we compress
given modified timestamp


Decompression algorithm  (reversing) explain
============================================


## Decompressing tracking code
Lets say Tracking code is 1BM2880af23....

### 1 Step

First character at tracking code - Type ID
Convert first character to number (use compression table). If, no pair exist
it means first character is the real number of type id.
1 is not in compression table so type id - 1
	
### 2 Step

#### Reversing single coordinate (latitude or longitude)

## Reversing coordinate integer part

**2 Possibilities:**
* Latitude integer part is coded in letter - 1st character  (second in tracking code)
* Latitude integer is not coded in letter - 2nd character s (second and third in tracking code)

if Latitude is not coded, then fourth character  is always "s". So firstly is checked if fourth
character  is "s" if yes - we take second and third  characters for integer part. If not
(like in this example) we take second character and convert it with compression table. This is 
coordinate is an integer part.

So Let's say we have coded letter B - 52. So our integer part = 52

## Reversing coordinate decimal part

Now we take rest part of the tracking code and reverting every characters TILL we have
6 digits.
So letter M will be 2 digit number (lets say 12). We take more 4 and have Senders
latitude coordinate 52.122880.


**the way described above is repeated for sender longitude, receiver latitude and longitude.**

Most important part here is that we know:
* where integer part starts / ends (1 letter or 2  digits)
* where decimal part starts ends   (6 digits)

## 3 Step

After step 2, the rest part of tracking code is compressed timestamp.


### Compressing Coordinate

Coordinate format - NN.XXXXXX
All most frequented  NN variations are defined in compress table.
If NN is not defined we add SEPERATOR between integer and decimal part.
Decimal part is coded like simple number.

### Timestamp

Originally timestamp has 10 digits. To reduce that size the algorithm 
substracts current time in seconds from other constant time in seconds
For example we define start timestamp (10 digits). By creating tracking 
code we substracting defined time from current timestamp. It reduces 2 
digits in range for 3 years.
Example 1:
Define start date  - 2018.01.01.     In unix timestmap - 1514764800
Lets say current date is 2018.02.01. In unix timestmap - 1517443200
Current date - Start date = 2678400 => only 7 digits
Example 2
Start date    2018.01.01 (1514764800)
Current date  2021.01.01 (1609459200)
Current date - Start date = 9.46944 × 10^7 => only 8 digits

That's proves that for 3 years we reduce 2 digits from timestamp.Even more we compress
given modified timestamp


## Compression Limitation

* Coordinates
  *  Europe coordinate boundary.

  *  Analysis showed that Europe has boundaries in range:

  *  Latitude in range (32;72)
  *  longitude in range (-10;42)

  *  algorithm  is able to compress coordinates in range:

  *  Latitude in range (-10;100)
  *  longitude in range (-10;100)

* Package type
  *  Amount of types (0-21)

* Date Limit
  *  3 years (then defined timestamp must be renewed or 1 digit to tracking code added)



#
# 1. Persistent and Database
#


Main DB structure:
```					
┌─────────────────────────────┐1..*    0..1┌────────────────────┐1..*     0..1┌─────────────────┐
│   parcels                   │───────────>│ parcel_status      │────────────>│      status     │
│─────────────────────────────│	           │────────────────────│             │─────────────────│
│PK     ID                    │            │FK  parcel_id       │             │PK  ID           │
│INDEX  tracking: VARCHAR 37  │            │FK  status_id       │             │    desc:VARCHAR │
│.                            │            │    createt_at      │             │    located:INT1 │
│.                            │            │    location:VARCHAR│             │                 │
│.                            │            │                    │             │                 │
└─────────────────────────────┘            └────────────────────┘             └─────────────────┘
```

Status Location explain
=======================

All needed information and data of Parcel could be saved om 'parcels' table.

'status' table contents all possible statuses. located column says if current status could be 

If yes then status desc column contents ...........[..#..]...... 

[..#..] - if location will be not entered this part of string will be 

[..#..] - if location will be entered - square brackets will be removed and # will be replaced with location.

#### Example:

*  Status desc - Die Sendung wurde [in #] sortiert
*  Form fill with location    - Hannover: Die Sendung wurde in Hannover sortiert
*  Form fill without location - Hannover: Die Sendung wurde sortiert

Location will be saved in 'pacel_status' pivot table with creation date. For more efficient could be created
other table 'locations' where all possible locations would be inserted and then by adding status use only
reference.
