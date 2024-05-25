CREATE USER IF NOT EXISTS 'root'@'%';
GRANT ALL ON *.* to 'root'@'%';
FLUSH PRIVILEGES;
ALTER USER 'root'@'%' IDENIFIED WITH mysql_native_password BY '123';
SET GLOBAL max_allowed_packet=524288000;