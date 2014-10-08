DROP TABLE IF EXISTS `tb_sponsor`;
DROP TABLE IF EXISTS `tb_news`;
DROP TABLE IF EXISTS `tb_matchchat`;
DROP TABLE IF EXISTS `tb_match`;
DROP TABLE IF EXISTS `tb_attendance`;
DROP TABLE IF EXISTS `tb_training`;
DROP TABLE IF EXISTS `tb_photo`;
DROP TABLE IF EXISTS `tb_video`;
DROP TABLE IF EXISTS `tb_gallery`;
DROP TABLE IF EXISTS `tb_chat`;
DROP TABLE IF EXISTS `tb_adminlog`;
DROP TABLE IF EXISTS `tb_user`;
DROP TABLE IF EXISTS `tb_config`;

CREATE TABLE `tb_user` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`email` varchar(60) NOT NULL DEFAULT '',
`password` varchar(200) NOT NULL DEFAULT '',
`active` tinyint(4) NOT NULL DEFAULT 1,
`salt` varchar(40) NOT NULL DEFAULT '',
`role` varchar(25) NOT NULL DEFAULT '',
`firstname` varchar(40) NOT NULL DEFAULT '',
`lastname` varchar(40) NOT NULL DEFAULT '',
`dob` varchar(12) NOT NULL DEFAULT '',
`playerNum` varchar(2) NOT NULL DEFAULT '',
`cfbuPersonalNum` varchar(15) NOT NULL DEFAULT '',
`team` varchar(20) NOT NULL DEFAULT '',
`nickname` varchar(30) NOT NULL DEFAULT '',
`photoMain` varchar(200) NOT NULL DEFAULT '',
`photoThumb` varchar(200) NOT NULL DEFAULT '',
`position` varchar(20) NOT NULL DEFAULT '',
`grip` varchar(5) NOT NULL DEFAULT '',
`other` text NOT NULL DEFAULT '',
`lastLogin` datetime DEFAULT NULL,
`loginLockdownTime` varchar(30) NOT NULL DEFAULT '',
`loginAttempCounter` tinyint(2) NOT NULL DEFAULT 0,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_user_login` (`email`, `password`, `active`),
UNIQUE KEY (`salt`),
UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_chat` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`reply` INT UNSIGNED DEFAULT 0,
`author` varchar(100) NOT NULL DEFAULT '',
`title` varchar(150) NOT NULL DEFAULT '',
`body` text,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_chat_reply` (`reply`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_gallery` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`urlKey` varchar(200) NOT NULL DEFAULT '',
`title` varchar(150) NOT NULL DEFAULT '',
`description` text NOT NULL DEFAULT '',
`isPublic` tinyint(4) NOT NULL DEFAULT 1,
`avatarPhotoId` INT UNSIGNED NOT NULL DEFAULT 0,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY (`urlKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_match` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`home` varchar(100) NOT NULL DEFAULT '',
`away` varchar(100) NOT NULL DEFAULT '',
`date` datetime DEFAULT NULL,
`hall` varchar(100) NOT NULL DEFAULT '',
`scoreHome` tinyint(4) NOT NULL DEFAULT -1,
`scoreAway` tinyint(4) NOT NULL DEFAULT -1,
`season` varchar(10) NOT NULL DEFAULT '',
`team` varchar(1) NOT NULL DEFAULT '',
`report` text NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_matchchat` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`matchId` INT UNSIGNED NOT NULL,
`active` tinyint(4) NOT NULL DEFAULT 1,
`reply` INT UNSIGNED DEFAULT 0,
`author` varchar(100) NOT NULL DEFAULT '',
`title` varchar(150) NOT NULL DEFAULT '',
`body` text NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_matchchat_reply` (`reply`),
FOREIGN KEY `fk_matchchat_match` (`matchId`) REFERENCES `tb_match` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_news` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `urlKey` varchar(200) NOT NULL DEFAULT '',
  `author` varchar(85) NOT NULL DEFAULT '',
  `title` varchar(150) NOT NULL DEFAULT '',
  `shortBody` text NOT NULL DEFAULT '',
  `body` text NOT NULL DEFAULT '',
  `expirationDate` varchar(22) NOT NULL DEFAULT '',
  `rank` tinyint(4) NOT NULL DEFAULT 1,
  `metaTitle` varchar(150) NOT NULL DEFAULT '',
  `metaDescription` varchar(255) NOT NULL DEFAULT '',
  `metaImage` varchar(255) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`urlKey`),
  KEY `ix_news_urlKey` (`urlKey`),
  KEY `ix_news_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_photo` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`galleryId` INT UNSIGNED NOT NULL,
`active` tinyint(4) NOT NULL DEFAULT 1,
`photoName` varchar(60) NOT NULL  DEFAULT '',
`imgThumb` varchar(250) NOT NULL  DEFAULT '',
`imgMain` varchar(250) NOT NULL  DEFAULT '',
`description` varchar(250) NOT NULL  DEFAULT '',
`priority` tinyint(4) DEFAULT 0,
`mime` varchar(32) NOT NULL DEFAULT '',
`format` varchar(10) NOT NULL DEFAULT '',
`size` INT NOT NULL DEFAULT 0,
`width` INT NOT NULL DEFAULT 0,
`height` INT NOT NULL DEFAULT 0,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY `fk_photo_gallery` (`galleryId`) REFERENCES `tb_gallery` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_video` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `title` varchar(150) NOT NULL,
  `path` varchar(250) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  `priority` tinyint(4) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_sponsor` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(150) NOT NULL DEFAULT '',
`web` varchar(150) NOT NULL DEFAULT '',
`logo` varchar(250) NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_training` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(150) NOT NULL DEFAULT '',
`location` varchar(100) NOT NULL DEFAULT '',
`startDate` varchar(12) NOT NULL DEFAULT '',
`startTime` varchar(12) NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_attendance` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`userId` INT UNSIGNED NOT NULL,
`trainingId` INT UNSIGNED NOT NULL,
`status` tinyint(4) NOT NULL DEFAULT 1,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY (`userId`, `trainingId`),
FOREIGN KEY `fk_attendance_user` (`userId`) REFERENCES `tb_user` (`id`),
FOREIGN KEY `fk_attendance_training` (`trainingId`) REFERENCES `tb_training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_adminlog` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` varchar(80) NOT NULL DEFAULT '',  
  `module` varchar(50) NOT NULL DEFAULT '',
  `controller` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(50) NOT NULL DEFAULT '',
  `result` varchar(15) NOT NULL DEFAULT '',
  `params` varchar(250) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '',
  `xkey` varchar(200) NOT NULL DEFAULT '',
  `value` varchar(250) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tb_config` VALUES (default, 'Application status', 'appstatus', 1, now(), default),
(default, 'Meta Keywords', 'meta_keywords', 'florbal, floorball, pedak', now(), default),
(default, 'Meta Description', 'meta_description', 'Web florbalového týmu VSSK Pedf UK A + B', now(), default),
(default, 'Meta Robots', 'meta_robots', 'NOINDEX,NOFOLLOW', now(), default),
(default, 'Meta Title', 'meta_title', 'Pedak - florbal', now(), default),
(default, 'Meta OG URL', 'meta_og_url', 'http://pedak.fear-team.cz', now(), default),
(default, 'Meta OG Type', 'meta_og_type', 'website', now(), default),
(default, 'Meta OG Site name', 'meta_og_site_name', 'Pedak - florbal', now(), default),
(default, 'Meta OG Image', 'meta_og_image', 'http://pedak.fear-team.cz/public/images/meta_image.jpg', now(), default),
(default, 'Photo thumb height', 'thumb_height', 200, now(), default),
(default, 'Photo thumb widht', 'thumb_width', 200, now(), default),
(default, 'Photo thumb resize by', 'thumb_resizeby', 'height', now(), default),
(default, 'Photo max height', 'photo_maxheight', 1080, now(), default),
(default, 'Photo max width', 'photo_maxwidth', 1920, now(), default);

insert into `tb_user` values (default, 'hodan.tomas@gmail.com', 
        '65c74e2954d3679248f2954b6672d7592d5894221c0ca7f37929761879057cffbba12f383049f924c4f3f76d8d46fad69fe491bef1e155ee77bd79a84642e67a', 
        default, 'mZTk4YTk5ZjAyMDk3NmE5NTMwOTA2NmFhMmRlMTc', 'role_superadmin', 'Tomáš', 'Hodáň', '26.7.1990', '44', '0709141461', 'a', default, 
        default, default, 'u', 'r', '', default, default, default, now(), null);

insert into `tb_user` values (default, 'tomasekslamak@seznam.cz', 
        '87a07c3eae0f8dc89ec2c33816a16516fa7f1cfef4e17a1e186c797af8cc235f08e180c7ca9ef1c93b2d0a4d284e886c65207fd2126eb41e3b0aea814bb3cda5', 
        default, 'kZDc0N2QwZTYwNzBlOGU5ZWYwOTkxNGMxNWQ1NzM', 'role_member', 'Tomáš', 'Sláma', '12.3.1991', '91', '0909170451', 'a', default, 
        default, default, 'o', 'l', '', default, default, default, now(), null);

insert into `tb_user` values (default, 'matauher@gmail.com', 
        'fd28d7195379f6389dddef508782555c807edf60ada97abcf7212166cf4b0f12a311f645afe56da5a6c94cbaacb110c8aae26d8e733b1593ade161d87fad7b97', 
        default, 'hNGZkNDU5NjlkZjVhODAwYzIyOGZlYmM4YjE5ZmY', 'role_member', 'Martin', 'Uher', '3.12.1993', '24', '1109080391', 'b', default, 
        default, default, 'u', 'r', '', default, default, default, now(), null);

INSERT INTO `tb_gallery` VALUES (default, default, 'news', 'News', 'Gallery for news photos and videos', 0, 0, now(), null);

insert into `tb_match` values 
        (default, default, 'FBK VŠSK PedF UK', 'BLACK ANGELS C', '2014-12-07 ', 'SH Děkanka', 2, 2, '2014/2015', 'a', '', now(), null),
        (default, default, 'FbK Wildcats Praha B', 'FBK VŠSK PedF UK', '2014-12-07', 'SH Děkanka', 7, 8, '2014/2015', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'ELITE PRAHA', '2015-01-04 09:30', 'SH Kralupy nad Vltavou', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'FBC Kralupy n/V', 'FBK VŠSK PedF UK', '2015-01-04 14:00', 'SH Kralupy nad Vltavou', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'FbK Wildcats Praha B', '2015-01-26', 'Unihoc Aréna Praha', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'Floorball Club Falcon B', 'FBK VŠSK PedF UK', '2015-01-26', 'Unihoc Aréna Praha', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'Fortel TJ Sokol Kobylisy B', 'FBK VŠSK PedF UK', '2015-02-16', 'TJ Sokol Kobylisy', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'Pallotaikurit', 'FBK VŠSK PedF UK', '2015-02-16', 'TJ Sokol Kobylisy', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'FC Bourbon SK Praha 4', '2015-04-05', 'SH Děkanka', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'EMCO Cholerics', 'FBK VŠSK PedF UK', '2015-04-05', 'SH Děkanka', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'Fortel TJ Sokol Kobylisy B', '2015-04-27', 'SH Stochov', default, default, '2014/2015', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'Floorball Club Falcon B', '2015-04-27', 'SH Stochov', default, default, '2014/2015', 'a', '', now(), null);

insert into `tb_match` values 
        (default, default, 'FBK VŠSK PedF UK B', 'Desperados SNW B', '2015-01-05 9:10', 'Sport Eden Beroun', 2, 0, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBC Vokovický Šavle', 'FBK VŠSK PedF UK B', '2015-01-05 11:30', 'Sport Eden Beroun', 4, 4, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBC Říčany', 'FBK VŠSK PedF UK B', '2015-01-19', 'SH Open Gate', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'FBC Vinořská Zvěř', '2015-01-19', 'SH Open Gate', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'FK Spartak Praha', 'FBK VŠSK PedF UK B', '2015-02-15', 'SH Čakovice', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'FBK Sokol Vlašim', '2015-03-08', 'ZŠ Jiráskova Benešov', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'SK Florbal Benešov B', 'FBK VŠSK PedF UK B', '2015-03-08', 'ZŠ Jiráskova Benešov', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'SK Partyzán Kamenice', '2015-04-05', 'Sport Eden Beroun', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBC Cavallino Rampante', 'FBK VŠSK PedF UK B', '2015-04-05', 'Sport Eden Beroun', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'FBC Vokovický Šavle', '2015-04-26', '	TJ Spartak Vlašim', default, default, '2014/2015', 'b', '', now(), null),
        (default, default, 'Desperados SNW B', 'FBK VŠSK PedF UK B', '2015-04-26', '	TJ Spartak Vlašim', default, default, '2014/2015', 'b', '', now(), null);

insert into tb_news values (default, default, 'bla-bla', 'Tomáš Hodáň', 'Lorem ipsum dolor sit amet', 
'Lorem ipsum dolor sit amet, justo labore pellentesque, vivamus in pellentesque tortor viverra vulputate massa, vehicula aliquam sed non. Est aliquam etiam nulla augue pede elit, eget elementum a orci vestibulum, dui vel tellus rhoncus. Nibh sodales ridiculus rhoncus. Convallis elit curabitur, ante morbi sit mauris et vel cupiditate, eros voluptatibus nec mauris mauris aliquam, commodo id ante mattis, ante adipiscing ullamcorper ligula lectus sed. Mauris rutrum tellus, venenatis elit, morbi enim consectetuer vehicula mauris pede diam, magna ipsum sed velit enim volutpat. Maecenas arcu, quam sed. Ultrices sed tincidunt wisi, aliquam velit nec, quis odio. Dictum dolor vestibulum bibendum.',
'Lorem ipsum dolor sit amet, justo labore pellentesque, vivamus in pellentesque tortor viverra vulputate massa, vehicula aliquam sed non. Est aliquam etiam nulla augue pede elit, eget elementum a orci vestibulum, dui vel tellus rhoncus. Nibh sodales ridiculus rhoncus. Convallis elit curabitur, ante morbi sit mauris et vel cupiditate, eros voluptatibus nec mauris mauris aliquam, commodo id ante mattis, ante adipiscing ullamcorper ligula lectus sed. Mauris rutrum tellus, venenatis elit, morbi enim consectetuer vehicula mauris pede diam, magna ipsum sed velit enim volutpat. Maecenas arcu, quam sed. Ultrices sed tincidunt wisi, aliquam velit nec, quis odio. Dictum dolor vestibulum bibendum.

 Fusce massa wisi et, vehicula a curabitur sit, etiam risus nec sed tristique, ipsum elit quisque, mauris saepe corporis. Pulvinar wisi malesuada ridiculus eget. Est nonummy nec, id suspendisse eu pellentesque vel praesent quis, donec saepe, amet volutpat imperdiet et malesuada nec felis, sed ut. Quisque adipiscing habitasse. Sollicitudin et magna sed risus vehicula auctor, ac ridiculus, nec ligula enim metus sem commodo risus, erat fringilla lobortis vulputate.

 Nunc vulputate sollicitudin duis, lacinia exercitation nisl pede. Ullamcorper donec qui neque quisque, purus bibendum eu, eu quis nunc varius libero est, lobortis ultrices, euismod ac. Praesent wisi nec viverra. Fermentum et adipiscing semper dui at aliquam. Vel convallis suspendisse enim a, id purus in suspendisse quis nam nec. Est facilis, a nec eget vehicula. Quia volutpat sit tempus tortor mollis, lacus ac voluptate ante felis id vehicula, curabitur cras vestibulum erat massa, mattis est leo, fringilla ac. Morbi habitasse, risus quam natoque massa dui molestie proin, orci justo praesent praesent velit etiam lorem.', 
'2015-05-05', default, 'bla bla', 'lorem ipsum dolor sit amet', default, now(), null);

insert into tb_news values (default, default, 'bla-bla2', 'Tomáš Hodáň', 'Lorem ipsum dolor sit amet2', 
'Lorem ipsum dolor sit amet, justo labore pellentesque, vivamus in pellentesque tortor viverra vulputate massa, vehicula aliquam sed non. Est aliquam etiam nulla augue pede elit, eget elementum a orci vestibulum, dui vel tellus rhoncus. Nibh sodales ridiculus rhoncus. Convallis elit curabitur, ante morbi sit mauris et vel cupiditate, eros voluptatibus nec mauris mauris aliquam, commodo id ante mattis, ante adipiscing ullamcorper ligula lectus sed. Mauris rutrum tellus, venenatis elit, morbi enim consectetuer vehicula mauris pede diam, magna ipsum sed velit enim volutpat. Maecenas arcu, quam sed. Ultrices sed tincidunt wisi, aliquam velit nec, quis odio. Dictum dolor vestibulum bibendum.',
'Lorem ipsum dolor sit amet, justo labore pellentesque, vivamus in pellentesque tortor viverra vulputate massa, vehicula aliquam sed non. Est aliquam etiam nulla augue pede elit, eget elementum a orci vestibulum, dui vel tellus rhoncus. Nibh sodales ridiculus rhoncus. Convallis elit curabitur, ante morbi sit mauris et vel cupiditate, eros voluptatibus nec mauris mauris aliquam, commodo id ante mattis, ante adipiscing ullamcorper ligula lectus sed. Mauris rutrum tellus, venenatis elit, morbi enim consectetuer vehicula mauris pede diam, magna ipsum sed velit enim volutpat. Maecenas arcu, quam sed. Ultrices sed tincidunt wisi, aliquam velit nec, quis odio. Dictum dolor vestibulum bibendum.

 Fusce massa wisi et, vehicula a curabitur sit, etiam risus nec sed tristique, ipsum elit quisque, mauris saepe corporis. Pulvinar wisi malesuada ridiculus eget. Est nonummy nec, id suspendisse eu pellentesque vel praesent quis, donec saepe, amet volutpat imperdiet et malesuada nec felis, sed ut. Quisque adipiscing habitasse. Sollicitudin et magna sed risus vehicula auctor, ac ridiculus, nec ligula enim metus sem commodo risus, erat fringilla lobortis vulputate.

 Nunc vulputate sollicitudin duis, lacinia exercitation nisl pede. Ullamcorper donec qui neque quisque, purus bibendum eu, eu quis nunc varius libero est, lobortis ultrices, euismod ac. Praesent wisi nec viverra. Fermentum et adipiscing semper dui at aliquam. Vel convallis suspendisse enim a, id purus in suspendisse quis nam nec. Est facilis, a nec eget vehicula. Quia volutpat sit tempus tortor mollis, lacus ac voluptate ante felis id vehicula, curabitur cras vestibulum erat massa, mattis est leo, fringilla ac. Morbi habitasse, risus quam natoque massa dui molestie proin, orci justo praesent praesent velit etiam lorem.', 
'2015-05-05', default, 'bla bla2', 'lorem ipsum dolor sit amet', default, now(), null);

insert into `tb_training` values 
        (default, default, 'Trenink UT', 'Neratovice', '2015-01-07', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-01-09', '19:30', now(), null),
        (default, default, 'Trenink UT', 'Neratovice', '2015-01-14', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-01-16', '19:30', now(), null),
        (default, default, 'Trenink UT', 'Neratovice', '2015-01-21', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-01-23', '19:30', now(), null),
        (default, default, 'Trenink UT', 'Neratovice', '2015-01-28', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-01-30', '19:30', now(), null),
        (default, default, 'Trenink UT', 'Neratovice', '2015-02-04', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-02-06', '19:30', now(), null),
        (default, default, 'Trenink UT', 'Neratovice', '2015-02-11', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-02-13', '19:30', now(), null),
        (default, default, 'Trenink UT', 'Neratovice', '2015-02-18', '19:30', now(), null),
        (default, default, 'Trenink CT', 'Neratovice', '2015-02-20', '19:30', now(), null);

insert into `tb_attendance` values (default, 1, 1, 1, now(), null),
        (default, 2, 1, 1, now(), null),
        (default, 1, 5, 1, now(), null),
        (default, 1, 6, 1, now(), null);

insert into `tb_sponsor` values 
(default, default, 'CEDES Logistik s.r.o.', 'http://www.cedeslogistik.cz/', '/public/uploads/sponsors/cedeslogo.gif', now(), null),
(default, default, 'Bazény Šulc', 'http://bazeny-sulc.cz/', '/public/uploads/sponsors/bazenysulclogo.png', now(), null);
