/* 0.8.3 Forums stats change primary key */
/*ALTER TABLE `cot_forum_stats` DROP PRIMARY KEY; */
ALTER TABLE `cot_forum_stats` DROP `fs_id`;
ALTER TABLE `cot_forum_stats` ADD PRIMARY KEY (`fs_cat`);


