
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
   email  varchar(64),
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
   type int default 1,
   token text,
   name varchar(64) not null,
   last_stream_ts varchar(16), 
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY  uniq_id  (source_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  fs_stream ;
CREATE TABLE  fs_stream  (
   id  int NOT NULL AUTO_INCREMENT,
   source_id  varchar(64) NOT NULL ,
   post_id  varchar(64) NOT NULL ,
   last_stream_ts varchar(16), 
   next_stream_ts varchar(16),
   d_bit int default 0 ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_post(post_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  fs_stream_tracker ;
CREATE TABLE  fs_stream_tracker  (
   id  int NOT NULL AUTO_INCREMENT,
   last_post_id varchar(64),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- 
-- seed fs_stream_tracker
-- 
insert into fs_stream_tracker (last_post_id,created_on) values (1,now() - interval 1 DAY) ;
update fs_stream_tracker set updated_on =  (now() - interval 1 DAY) ;




DROP TABLE IF EXISTS  fs_post ;
CREATE TABLE  fs_post  (
   id  int NOT NULL AUTO_INCREMENT,
   source_id  varchar(64) NOT NULL ,
   post_id  varchar(64) NOT NULL ,
   picture text,
   link text,
   object_id varchar(64),
   message varchar(256),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_post(post_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  fs_comment ;
CREATE TABLE  fs_comment  (
  id  int NOT NULL AUTO_INCREMENT,
  source_id  varchar(64) NOT NULL ,
  post_id  varchar(64) NOT NULL ,
  from_id varchar(64) not null,
  user_name varchar(64) not null,
  message varchar(256),
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;




  