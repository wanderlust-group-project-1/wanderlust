FROM mysql:8.2

COPY ./database/wanderlust.sql /docker-entrypoint-initdb.d/wanderlust.sql