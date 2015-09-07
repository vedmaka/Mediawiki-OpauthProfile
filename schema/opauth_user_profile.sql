CREATE TABLE /*_*/opauth_user_profile (
  user_id int NOT NULL,
  name varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  nickname varchar(255) DEFAULT NULL,
  first_name varchar(255) DEFAULT NULL,
  last_name varchar(255) DEFAULT NULL,
  location varchar(255) DEFAULT NULL,
  description varchar(1024) DEFAULT NULL,
  image varchar(255) DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  provider varchar(255) DEFAULT NULL,
  uid varchar(255) DEFAULT NULL
) /*$wgDbTableOptions*/ ;