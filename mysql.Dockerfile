FROM mysql:8.1

COPY ./database/wanderlust.sql /docker-entrypoint-initdb.d/wanderlust.sql