FROM mysql:9.0

COPY ./database/wanderlust.sql /docker-entrypoint-initdb.d/wanderlust.sql