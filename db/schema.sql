
DROP TABLE IF EXISTS  sc_php_session ;
CREATE TABLE  sc_php_session  (
   session_id  varchar(40) NOT NULL DEFAULT '',
   data  text,
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( session_id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  fs_login ;
CREATE TABLE  fs_login  (
   id  int NOT NULL AUTO_INCREMENT,
   name  varchar(32) NOT NULL,
   source  int default 1,
   access_token text ,
   ip_address varchar(46),
   session_id varchar(40),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   expire_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  fs_facebook_user ;
CREATE TABLE  fs_facebook_user  (
   id  int NOT NULL AUTO_INCREMENT,
   facebook_id  varchar(64) NOT NULL ,
   login_id  int(11) NOT NULL,
   name  varchar(64) NOT NULL,
   first_name  varchar(32) ,
   last_name  varchar(32) ,
   email  varchar(64) ,
   ip_address varchar(46),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( facebook_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  fs_source ;
CREATE TABLE  fs_source  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id int not null,
   source_id  varchar(64) NOT NULL ,
   source_type int default 1,
   source_token text,
   source_name varchar(64) not null,
   last_update_ts varchar(16), 
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY  uniq_id  (source_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

