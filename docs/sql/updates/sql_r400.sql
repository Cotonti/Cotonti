/* r400 change poll type */ 
ALTER TABLE sed_polls MODIFY poll_type VARCHAR(100) NOT NULL DEFAULT 'index';
UPDATE sed_polls SET poll_type = 'index' WHERE poll_type = '0';
UPDATE sed_polls SET poll_type = 'forum' WHERE poll_type = '1';