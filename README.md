# Wanderlust

## Camping Equipment Rental and Guide Booking System



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

## import database

```bash
docker exec -it wl-mysql bash

# inside container
cd docker-entrypoint-initdb.d/
# On Windows

sed -i -e 's/\r$//' .env
sed -i -e 's/\r$//' migrate.sh

#import
./migrate.sh import
```


## Compile SCSS

Use Live Sass Compiler Extension in VSCode to compile SCSS to CSS

