/* Update to v 1.1.3 */
ALTER TABLE `cot_forum_posts` DROP INDEX `fp_topicid`, ADD UNIQUE fp_topicid_id_idx (fp_topicid, fp_id) USING BTREE;
-- ADD index for topicid
ALTER TABLE `cot_forum_posts` ADD INDEX fp_topicid_idx (fp_topicid);
