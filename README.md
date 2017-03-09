# _RSVParty_

#### _An event planning app, March 9th, 2017_

#### By _**Drew Devlin, Keith Evans, Brynna Klamkin-McCarter, and Katharyn Reed**_

## Description

_This app allows users to create and plan events. They can then send invites to potential attendees who can RSVP. The event page also gives users and attendees a map detailing the location of the planned event._

## Setup/Installation Requirements

* _Navigate to the desktop in your terminal using `cd desktop`_
* _Clone GitHub repository to the desktop with `git clone (GitHub repo URL)`_
* _Navigate to main project folder using `cd rsvparty`_
* _Run `composer install` to install all dependencies_
* _Start MAMP and go to your phpMyAdmin page_
* _Click the import tab at the top of the page and import the .sql.zip files located in the main project directory_
* _Ensure that the database port in app/app.php matches your default MySQL port_
* _In your web browser, enter 'localhost:8888' or your default apache server port in the URL bar_

## Known Bugs

_There are currently no known bugs._

## Database SQL Constructors

CREATE DATABASE rsvparty_test;
USE rsvparty_test;
CREATE TABLE users (id serial PRIMARY KEY, name VARCHAR (255), password VARCHAR (255), email VARCHAR (255));
CREATE TABLE events (id serial PRIMARY KEY, user_id INT, name VARCHAR (255), date_time DATETIME, description VARCHAR (255), location VARCHAR (255), guest_key VARCHAR (255));
CREATE TABLE attendees (id serial PRIMARY KEY, name VARCHAR (255), email VARCHAR (255), event_id INT, rsvp BOOLEAN);
CREATE DATABASE rsvparty;
USE rsvparty;
CREATE TABLE users (id serial PRIMARY KEY, name VARCHAR (255), password VARCHAR (255), email VARCHAR (255));
CREATE TABLE events (id serial PRIMARY KEY, user_id INT, name VARCHAR (255), date_time DATETIME, description VARCHAR (255), location VARCHAR (255), guest_key VARCHAR (255));
CREATE TABLE attendees (id serial PRIMARY KEY, name VARCHAR (255), email VARCHAR (255), event_id INT, rsvp BOOLEAN);

## Support and contact details

_Any questions, comments, or bug reports can be directed to the repository administrator._

## Technologies Used

_This app was built in the PHP/Silex framework using Twig for templating. It includes the Google Maps API._

### License

*This application is under the MIT License.*

Copyright (c) 2017 **_Drew Devlin, Keith Evans, Brynna Klamkin-McCarter, and Katharyn Reed_**
