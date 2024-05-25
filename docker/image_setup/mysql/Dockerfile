FROM mysql:8.0

COPY ./resources/01-mysql-modes.cnf /etc/mysql/conf.d/01-modes.cnf
COPY resources/init-root-user.sql /docker-entrypoint-initdb.d/init-root-user.sql
