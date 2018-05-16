/* TODO: create tables */

/* Events table */
CREATE TABLE `events` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`title`	TEXT NOT NULL,
	`date`	TEXT NOT NULL,
	`start_time`	TEXT NOT NULL,
	`location`	TEXT NOT NULL,
	`description`	TEXT
);

/* Members table */
CREATE TABLE `members` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`first_name`	TEXT NOT NULL,
	`last_name`	TEXT NOT NULL,
	`netid`	TEXT NOT NULL UNIQUE
);

/* Images table */
CREATE TABLE `images` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`file_name`	TEXT NOT NULL,
	`file_ext`	TEXT NOT NULL
);

/* Users table */
CREATE TABLE `users` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`username`	TEXT NOT NULL UNIQUE,
	`password`	TEXT NOT NULL
);

/* Homepage table */
CREATE TABLE `homepage` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`about`	TEXT NOT NULL,
	`history`	TEXT NOT NULL,
	`background`	TEXT NOT NULL,
	`background_ext`	TEXT NOT NULL,
	`background_desc` TEXT NOT NULL,
	`source` TEXT NOT NULL
);

/* TODO: initial seed data */

/* Events seed data */
/* FORMAT dates like: 01-15-1998. FORMAT times like: 03:30 PM */
INSERT INTO `events` (title, date, start_time, location, description) VALUES ('Challah Baking!', '04-22-2018', '05:00 PM', '158 Stocking Hall', 'Come celebrate (hopefully) the first week of market with us as we make our market favorite, challah!
Head over to 158 Stocking Hall at 5pm this Wednesday to bake with us! RSVP to jag538@cornell.edu if you plan on coming!');

INSERT INTO `events` (title, date, start_time, location, description) VALUES ('Cinnamon Rolls', '03-12-2018', '05:00 PM', '158 Stocking Hall', 'We''re making cinnamon rolls this week! It''s going to be a long meeting because these loaves have three rises, but they''re definitely worth the wait! If you give your friends some of your rolls, you''ll be the boule of the ball!');
INSERT INTO `events` (title, date, start_time, location, description) VALUES ('Stromboli', '02-14-2018', '05:00 PM', '158 Stocking Hall', 'Stromboli is the perfect bread to have with your significant other, share with your roommates, or eat by yourself (because you deserve it)!
Come celebrate Valentine''s Day with us this Wednesday at 5pm in 158 Stocking Hall and make stromboli! Feel free to bring your own fillings, we''ll have cheese and some others!');
INSERT INTO `events` (title, date, start_time, location, description) VALUES ('Foccacia', '01-30-2018', '04:00 PM', '158 Stocking Hall', 'Tired of wheating to get your bread game back on this semester? Well, Bread Club is back! Come make focaccia with us this Wednesday!');
INSERT INTO `events` (title, date, start_time, location, description) VALUES ('Rolls!', '05-20-2018', '05:00 PM', '158 Stocking Hall', 'Bored of finals? Come ROLL on by and make some fun rolls! We''ll be baking pretzel rolls, salt rolls, and more!');
INSERT INTO `events` (title, date, start_time, location, description) VALUES ('Welcome Back Meeting', '09-01-2018', '03:00 PM', '158 Stocking Hall', 'Come celebrate the start of the semester and make some wheat bread! Great to eat as toast with jams!');


/* Members seed data */
INSERT INTO `members` (first_name, last_name, netid) VALUES ('Will', 'Wang', 'wow7');
INSERT INTO `members` (first_name, last_name, netid) VALUES ('Rowan', 'Johnson', 'rej77');
INSERT INTO `members` (first_name, last_name, netid) VALUES ('Ariel', 'Lin', 'al2248');
INSERT INTO `members` (first_name, last_name, netid) VALUES ('John', 'Smith', 'js123');
INSERT INTO `members` (first_name, last_name, netid) VALUES ('Mary', 'Jane', 'mj28');

/* Images seed data */
INSERT INTO `images` (file_name, file_ext) VALUES ('1', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('2', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('3', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('4', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('5', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('6', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('7', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('8', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('9', 'jpg');
INSERT INTO `images` (file_name, file_ext) VALUES ('10', 'jpg');

/* User seed data */
/* admin: password */
INSERT INTO `users` (username, password) VALUES ('admin', '$2y$10$KGyI.BF5jCML/msJ3h9CV.WBjoteOhuvRanCWAWHBDSZss8v4fvfK');

/* Homepage seed data */
INSERT INTO `homepage` (about, history, background, background_ext, background_desc, source) VALUES
('We are the Bread Club of Cornell University in Ithaca, NY. We are a forum for bread fans to gather, meet each other, make bread, learn about bread, and share experiences.
	We sell our bread at the Farmers Market at Cornell on Thursdays 11-3 on the Ag Quad.',
 'Founded in 2014, we formed with the mission to bring fresh, delicious home-baked bread to the Cornell Community, as well as to
 provide a space for bread lovers to meet and cook together. Our founding members created the club to be open to any member of the
 Cornell Community interested in baking bread, no matter their skill level or experience.' , 'bg', 'jpg',
 'Bread on Teal Background','https://www.delish.com/cooking/g1859/easy-quick-bread-recipes/');
