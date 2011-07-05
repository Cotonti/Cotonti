/* r1324 forum topics polls */
INSERT INTO `cot_plugins` ( `pl_hook` , `pl_code` , `pl_part` , `pl_title` , `pl_file` , `pl_order` , `pl_active` , `pl_module` )
VALUES ('forums.topics.first', 'polls', 'forums.include', 'Polls', './modules/polls/polls.forums.include.php', '10', '1', '1');