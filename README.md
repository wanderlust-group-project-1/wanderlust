# mvc

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
