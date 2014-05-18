DROP TABLE IF EXISTS `tb_sponsor`;
DROP TABLE IF EXISTS `tb_link`;
DROP TABLE IF EXISTS `tb_newsphoto`;
DROP TABLE IF EXISTS `tb_news`;
DROP TABLE IF EXISTS `tb_matchchat`;
DROP TABLE IF EXISTS `tb_match`;
DROP TABLE IF EXISTS `tb_attendance`;
DROP TABLE IF EXISTS `tb_training`;
DROP TABLE IF EXISTS `tb_photo`;
DROP TABLE IF EXISTS `tb_gallery`;
DROP TABLE IF EXISTS `tb_chat`;
DROP TABLE IF EXISTS `tb_user`;

CREATE TABLE `tb_user` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`email` varchar(100) NOT NULL DEFAULT '',
`password` varchar(130) NOT NULL DEFAULT '',
`active` tinyint(4) NOT NULL DEFAULT 1,
`role` varchar(25) NOT NULL DEFAULT '',
`firstname` varchar(50) NOT NULL DEFAULT '',
`lastname` varchar(50) NOT NULL DEFAULT '',
`dob` varchar(12) NOT NULL DEFAULT '',
`playerNum` varchar(2) NOT NULL DEFAULT '',
`cfbuPersonalNum` varchar(15) NOT NULL DEFAULT '',
`team` varchar(20) NOT NULL DEFAULT '',
`nickname` varchar(30) NOT NULL DEFAULT '',
`photo` varchar(200) NOT NULL DEFAULT '',
`position` varchar(20) NOT NULL DEFAULT '',
`grip` varchar(5) NOT NULL DEFAULT '',
`other` text,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_user_active` (`active`),
KEY `ix_user_login` (`email`, `password`, `active`),
UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_chat` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`reply` int(11) UNSIGNED DEFAULT 0,
`author` varchar(100) NOT NULL DEFAULT '',
`title` varchar(100) NOT NULL DEFAULT '',
`body` text,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_chat_active` (`active`),
KEY `ix_chat_reply` (`reply`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_gallery` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(100) NOT NULL DEFAULT '',
`description` text,
`avatar` varchar(150) NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_gallery_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_match` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`home` varchar(100) NOT NULL DEFAULT '',
`host` varchar(100) NOT NULL DEFAULT '',
`date` datetime DEFAULT NULL,
`hall` varchar(100) NOT NULL DEFAULT '',
`scoreHome` tinyint(4) NOT NULL DEFAULT -1,
`scoreHost` tinyint(4) NOT NULL DEFAULT -1,
`season` varchar(10) NOT NULL DEFAULT '',
`team` varchar(1) NOT NULL DEFAULT '',
`report` text,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_match_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_matchchat` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`matchId` int(11) UNSIGNED NOT NULL,
`active` tinyint(4) NOT NULL DEFAULT 1,
`reply` int(11) UNSIGNED DEFAULT 0,
`author` varchar(100) NOT NULL DEFAULT '',
`title` varchar(100) NOT NULL DEFAULT '',
`body` text,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_matchchat_match` (`matchId`),
KEY `ix_matchchat_active` (`active`),
KEY `ix_matchchat_reply` (`reply`),
FOREIGN KEY `fk_matchchat_match` (`matchId`) REFERENCES `tb_match` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_news` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`author` varchar(100) NOT NULL DEFAULT '',
`title` varchar(100) NOT NULL DEFAULT '',
`body` text,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_news_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_photo` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`galleryId` int(11) UNSIGNED NOT NULL,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(100) NOT NULL DEFAULT '',
`photoName` varchar(100) NOT NULL DEFAULT '',
`pathSmall` varchar(150) NOT NULL DEFAULT '',
`pathLarge` varchar(150) NOT NULL DEFAULT '',
`mime` varchar(32) NOT NULL DEFAULT '',
`size` int(11) NOT NULL DEFAULT 0,
`width` int(11) NOT NULL DEFAULT 0,
`height` int(11) NOT NULL DEFAULT 0,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_photo_gallery` (`galleryId`),
KEY `ix_photo_active` (`active`),
FOREIGN KEY `fk_photo_gallery` (`galleryId`) REFERENCES `tb_gallery` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_sponsor` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(100) NOT NULL DEFAULT '',
`url` varchar(150) NOT NULL DEFAULT '',
`logo` varchar(150) NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_sponsor_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_link` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(100) NOT NULL DEFAULT '',
`url` varchar(150) NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_link_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_training` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`active` tinyint(4) NOT NULL DEFAULT 1,
`title` varchar(100) NOT NULL DEFAULT '',
`date` varchar(12) NOT NULL DEFAULT '',
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_training_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_attendance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`userId` int(11) UNSIGNED NOT NULL,
`trainingId` int(11) UNSIGNED NOT NULL,
`active` tinyint(4) NOT NULL DEFAULT 1,
`status` tinyint(4) NOT NULL DEFAULT 1,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY (`userId`, `trainingId`),
KEY `ix_attendance_user` (`userId`),
KEY `ix_attendance_training` (`trainingId`),
KEY `ix_attendance_active` (`active`),
FOREIGN KEY `fk_attendance_user` (`userId`) REFERENCES `tb_user` (`id`),
FOREIGN KEY `fk_attendance_training` (`trainingId`) REFERENCES `tb_training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tb_newsphoto` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`newsId` int(11) UNSIGNED NOT NULL,
`photoId` int(11) UNSIGNED NOT NULL,
`active` tinyint(4) NOT NULL DEFAULT 1,
`created` datetime DEFAULT NULL,
`modified` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ix_newsphoto_news` (`newsId`),
KEY `ix_newsphoto_photo` (`photoId`),
KEY `ix_newsphoto_active` (`active`),
UNIQUE KEY (`newsId`, `photoId`),
FOREIGN KEY `fk_newsphoto_news` (`newsId`) REFERENCES `tb_news` (`id`) ON DELETE CASCADE,
FOREIGN KEY `fk_newsphoto_photo` (`photoId`) REFERENCES `tb_photo` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `tb_user` values (default, 'hodan.tomas@gmail.com', 
'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 
        default, 'role_superadmin', 'Tomáš', 'Hodáň', '26.7.1990', '44', '0709141461', 'a', default, 
        default, 'u', 'r', '', now(), null);

insert into `tb_user` values (default, 'tomasekslamak@seznam.cz', 
'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 
        default, 'role_member', 'Tomáš', 'Sláma', '12.3.1991', '91', '0909170451', 'a', default, 
        default, 'o', 'l', '', now(), null);

insert into `tb_user` values (default, 'matauher@gmail.com', 
'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 
        default, 'role_member', 'Martin', 'Uher', '3.12.1993', '24', '1109080391', 'b', default, 
        default, 'u', 'r', '', now(), null);

insert into `tb_match` values 
        (default, default, 'FBK VŠSK PedF UK', 'BLACK ANGELS C', '2013-12-07 ', 'SH Děkanka', 2, 2, '2013/2014', 'a', '', now(), null),
        (default, default, 'FbK Wildcats Praha B', 'FBK VŠSK PedF UK', '2013-12-07', 'SH Děkanka', 7, 8, '2013/2014', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'ELITE PRAHA', '2014-01-04 09:30', 'SH Kralupy nad Vltavou', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'FBC Kralupy n/V', 'FBK VŠSK PedF UK', '2014-01-04 14:00', 'SH Kralupy nad Vltavou', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'FbK Wildcats Praha B', '2014-01-26', 'Unihoc Aréna Praha', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'Floorball Club Falcon B', 'FBK VŠSK PedF UK', '2014-01-26', 'Unihoc Aréna Praha', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'Fortel TJ Sokol Kobylisy B', 'FBK VŠSK PedF UK', '2014-02-16', 'TJ Sokol Kobylisy', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'Pallotaikurit', 'FBK VŠSK PedF UK', '2014-02-16', 'TJ Sokol Kobylisy', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'FC Bourbon SK Praha 4', '2014-04-05', 'SH Děkanka', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'EMCO Cholerics', 'FBK VŠSK PedF UK', '2014-04-05', 'SH Děkanka', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'Fortel TJ Sokol Kobylisy B', '2014-04-27', 'SH Stochov', default, default, '2013/2014', 'a', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK', 'Floorball Club Falcon B', '2014-04-27', 'SH Stochov', default, default, '2013/2014', 'a', '', now(), null);

insert into `tb_match` values 
        (default, default, 'FBK VŠSK PedF UK B', 'Desperados SNW B', '2014-01-05 9:10', 'Sport Eden Beroun', 2, 0, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBC Vokovický Šavle', 'FBK VŠSK PedF UK B', '2014-01-05 11:30', 'Sport Eden Beroun', 4, 4, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBC Říčany', 'FBK VŠSK PedF UK B', '2014-01-19', 'SH Open Gate', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'FBC Vinořská Zvěř', '2014-01-19', 'SH Open Gate', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'FK Spartak Praha', 'FBK VŠSK PedF UK B', '2014-02-15', 'SH Čakovice', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'FBK Sokol Vlašim', '2014-03-08', 'ZŠ Jiráskova Benešov', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'SK Florbal Benešov B', 'FBK VŠSK PedF UK B', '2014-03-08', 'ZŠ Jiráskova Benešov', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'SK Partyzán Kamenice', '2014-04-05', 'Sport Eden Beroun', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBC Cavallino Rampante', 'FBK VŠSK PedF UK B', '2014-04-05', 'Sport Eden Beroun', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'FBK VŠSK PedF UK B', 'FBC Vokovický Šavle', '2014-04-26', '	TJ Spartak Vlašim', default, default, '2013/2014', 'b', '', now(), null),
        (default, default, 'Desperados SNW B', 'FBK VŠSK PedF UK B', '2014-04-26', '	TJ Spartak Vlašim', default, default, '2013/2014', 'b', '', now(), null);

insert into tb_news values (default, default, 'Tomáš Hodáň', 'Lorem ipsum dolor sit amet', 
'Lorem ipsum dolor sit amet, justo labore pellentesque, vivamus in pellentesque tortor viverra vulputate massa, vehicula aliquam sed non. Est aliquam etiam nulla augue pede elit, eget elementum a orci vestibulum, dui vel tellus rhoncus. Nibh sodales ridiculus rhoncus. Convallis elit curabitur, ante morbi sit mauris et vel cupiditate, eros voluptatibus nec mauris mauris aliquam, commodo id ante mattis, ante adipiscing ullamcorper ligula lectus sed. Mauris rutrum tellus, venenatis elit, morbi enim consectetuer vehicula mauris pede diam, magna ipsum sed velit enim volutpat. Maecenas arcu, quam sed. Ultrices sed tincidunt wisi, aliquam velit nec, quis odio. Dictum dolor vestibulum bibendum.

 Fusce massa wisi et, vehicula a curabitur sit, etiam risus nec sed tristique, ipsum elit quisque, mauris saepe corporis. Pulvinar wisi malesuada ridiculus eget. Est nonummy nec, id suspendisse eu pellentesque vel praesent quis, donec saepe, amet volutpat imperdiet et malesuada nec felis, sed ut. Quisque adipiscing habitasse. Sollicitudin et magna sed risus vehicula auctor, ac ridiculus, nec ligula enim metus sem commodo risus, erat fringilla lobortis vulputate.

 Nunc vulputate sollicitudin duis, lacinia exercitation nisl pede. Ullamcorper donec qui neque quisque, purus bibendum eu, eu quis nunc varius libero est, lobortis ultrices, euismod ac. Praesent wisi nec viverra. Fermentum et adipiscing semper dui at aliquam. Vel convallis suspendisse enim a, id purus in suspendisse quis nam nec. Est facilis, a nec eget vehicula. Quia volutpat sit tempus tortor mollis, lacus ac voluptate ante felis id vehicula, curabitur cras vestibulum erat massa, mattis est leo, fringilla ac. Morbi habitasse, risus quam natoque massa dui molestie proin, orci justo praesent praesent velit etiam lorem.', now(), null);
insert into tb_news values (default, default, 'Tomáš Hodáň', 'Lorem ipsum dolor sit amet2', 
'Lorem ipsum dolor sit amet, justo labore pellentesque, vivamus in pellentesque tortor viverra vulputate massa, vehicula aliquam sed non. Est aliquam etiam nulla augue pede elit, eget elementum a orci vestibulum, dui vel tellus rhoncus. Nibh sodales ridiculus rhoncus. Convallis elit curabitur, ante morbi sit mauris et vel cupiditate, eros voluptatibus nec mauris mauris aliquam, commodo id ante mattis, ante adipiscing ullamcorper ligula lectus sed. Mauris rutrum tellus, venenatis elit, morbi enim consectetuer vehicula mauris pede diam, magna ipsum sed velit enim volutpat. Maecenas arcu, quam sed. Ultrices sed tincidunt wisi, aliquam velit nec, quis odio. Dictum dolor vestibulum bibendum.

 Fusce massa wisi et, vehicula a curabitur sit, etiam risus nec sed tristique, ipsum elit quisque, mauris saepe corporis. Pulvinar wisi malesuada ridiculus eget. Est nonummy nec, id suspendisse eu pellentesque vel praesent quis, donec saepe, amet volutpat imperdiet et malesuada nec felis, sed ut. Quisque adipiscing habitasse. Sollicitudin et magna sed risus vehicula auctor, ac ridiculus, nec ligula enim metus sem commodo risus, erat fringilla lobortis vulputate.

 Nunc vulputate sollicitudin duis, lacinia exercitation nisl pede. Ullamcorper donec qui neque quisque, purus bibendum eu, eu quis nunc varius libero est, lobortis ultrices, euismod ac. Praesent wisi nec viverra. Fermentum et adipiscing semper dui at aliquam. Vel convallis suspendisse enim a, id purus in suspendisse quis nam nec. Est facilis, a nec eget vehicula. Quia volutpat sit tempus tortor mollis, lacus ac voluptate ante felis id vehicula, curabitur cras vestibulum erat massa, mattis est leo, fringilla ac. Morbi habitasse, risus quam natoque massa dui molestie proin, orci justo praesent praesent velit etiam lorem.', now(), null);

insert into `tb_training` values (default, default, 'Trenink UT', '2014-01-07', now(), null),
        (default, default, 'Trenink CT', '2014-01-09', now(), null),
        (default, default, 'Trenink UT', '2014-01-14', now(), null),
        (default, default, 'Trenink CT', '2014-01-16', now(), null),
        (default, default, 'Trenink UT', '2014-01-21', now(), null),
        (default, default, 'Trenink CT', '2014-01-23', now(), null),
        (default, default, 'Trenink UT', '2014-01-28', now(), null),
        (default, default, 'Trenink CT', '2014-01-30', now(), null),
        (default, default, 'Trenink UT', '2014-02-04', now(), null),
        (default, default, 'Trenink CT', '2014-02-06', now(), null),
        (default, default, 'Trenink UT', '2014-02-11', now(), null),
        (default, default, 'Trenink CT', '2014-02-13', now(), null),
        (default, default, 'Trenink UT', '2014-02-18', now(), null),
        (default, default, 'Trenink CT', '2014-02-20', now(), null);

insert into `tb_attendance` values (default, 1, 1, default, 1, now(), null),
        (default, 1, 3, default, 1, now(), null),
        (default, 1, 5, default, 1, now(), null),
        (default, 1, 6, default, 1, now(), null);

insert into `tb_sponsor` values 
(default, default, 'CEDES Logistik s.r.o.', 'http://www.cedeslogistik.cz/', '/public/uploads/sponsors/cedeslogo.gif', now(), null),
(default, default, 'Bazény Šulc', 'http://bazeny-sulc.cz/', '/public/uploads/sponsors/bazenysulclogo.png', now(), null);