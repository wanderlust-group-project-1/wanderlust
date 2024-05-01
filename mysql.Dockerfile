FROM mysql:8.4

COPY ./database/wanderlust.sql /docker-entrypoint-initdb.d/wanderlust.sql