create database fsdb1  character set utf8 collate utf8_general_ci ;
grant all privileges on fsdb1.* to 'gloo'@'localhost' identified by 'osje8L' with grant option;

-- needed if mysql binds to a LAN IP
-- grant all privileges on fsdb1.* to 'gloo'@'10.178.225.240' identified by 'osje8L' with grant option;

