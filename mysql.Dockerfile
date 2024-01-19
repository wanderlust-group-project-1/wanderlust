FROM mysql:8.3

COPY ./database/wanderlust.sql /docker-entrypoint-initdb.d/wanderlust.sql