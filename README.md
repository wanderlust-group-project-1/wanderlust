# Wanderlust

## Camping Equipment Renting and Guide Booking System


<p align="center">
  <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/Logo.png?raw=true" alt="Wanderlust Logo"  height="200">
</p>



## Group Members 

- [Nirmal Savinda](https://www.github.com/nsavinda)
- [Gayandee Rajapaksha](https://www.github.com/Gayandee)
- [Sarani Hettiarachchi](https://www.github.com/Zaras00)
- [Sandali Gunawardena](https://www.github.com/Sandali-Upekha)


## Features

### For Rental Service

- Inventory Management
- Rental Management
- Complaint Management
- Report Generation


### For Guide

- Package Management
- Booking Calendar 
- Report Generation


### For Customer

- Booking & Rent Equipment
- Booking Guide
- Complaint Management


### For Admin

- User Management
- View Statistics
- Handle Complaints
- Report Generation



## Screenshots

<!-- Center 2 column layout -->
<p align="center">
  <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/1.png?raw=true"  width="45%">
    <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/2.png?raw=true" width="45%">
    <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/3.png?raw=true"  width="45%">
        <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/4.png?raw=true"  width="45%">
    <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/5.png?raw=true"  width="45%">
    <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/6.png?raw=true"  width="45%">
    <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/7.png?raw=true"  width="45%">
        <img src="https://github.com/wanderlust-group-project-1/wanderlust/blob/main/docs/8.png?raw=true"  width="45%">



</p>

## Tech Stack

- HTML | JavaScript | jQuery | SCSS | CSS
- PHP
- MySQL
- Apache
- Docker


### APIs and Other Integrations

- Google Maps API
- Payhere Sandbox
- PHPMailer
- Dompdf
- ApexCharts


# Setup

Rename `example.env` to `.env`

Rename `database/example.env` to `database/.env`



```bash
docker-compose up -d --build
```

Site will be available at `http://localhost:8000`

## import database

```bash
docker exec -it wl-mysql bash

# inside container
cd docker-entrypoint-initdb.d/

# On Windows (To remove CRLF)
sed -i -e 's/\r$//' .env
sed -i -e 's/\r$//' migrate.sh

#import
./migrate.sh import
```
Or open PHPMyAdmin at `http://localhost:8007` and import the `wanderlust.sql` file

## Compile SCSS

Use Live Sass Compiler Extension in VSCode to compile SCSS to CSS

## Login Details

customer@wl.com | Admin@1234

rental@wl.com | Admin@1234

guide@wl.com | Admin@1234

admin@wl.com | Admin1234