DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  username varchar(50) NOT NULL,
  password varchar(100) NOT NULL,
  enabled int NOT NULL,
  super int NOT NULL,
  PRIMARY KEY (`username`)
);

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  postid int NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  post varchar(300) NOT NULL,
  posted_date varchar(100) NOT NULL,
  PRIMARY KEY (`postid`)
);

INSERT INTO `users` VALUES ('admin', password('admin'), 1, 1);