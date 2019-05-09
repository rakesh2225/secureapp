
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  username varchar(50) NOT NULL,
  password varchar(100) NOT NULL,
  enabled int NOT NULL,
  super int NOT NULL,
  PRIMARY KEY (`username`)
);

CREATE TABLE `posts` (
  postid int NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  post varchar(300) NOT NULL,
  posted_date varchar(100) NOT NULL,
  PRIMARY KEY (`postid`),
  FOREIGN KEY (`username`) REFERENCES users(`username`) ON DELETE CASCADE
);

CREATE TABLE `comments` (
  commentid int NOT NULL AUTO_INCREMENT,
  postid int NOT NULL,
  username varchar(50) NOT NULL,
  comment varchar(300) NOT NULL,
  commented_date varchar(100) NOT NULL,
  PRIMARY KEY (`commentid`),
  FOREIGN KEY (`postid`) REFERENCES posts(`postid`) ON DELETE CASCADE
);

INSERT INTO `users` VALUES ('admin', password('admin'), 1, 1);