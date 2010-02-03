Since Cotonti Siena r1105 updates are organized this way:
- This setup folder has subfolders for each branch (major version)
- Within each branch patches are revision-based
- SQL patches for revisions are named sql_r{revision_number}.sql
- PHP patches for revisions are named php_r{revision_number}.inc
- SQL patch for moving from previous branch to current branch
has name patch-{previous_branch_name}.sql
- PHP patch for moving from previous branch to current branch
has name patch-{previous_branch_name}.inc
- Blank installation SQL file is called install.sql
- If you make changes in SQL, you apply them on all 3 files
(if necessary): sql_r{rev}.sql, patch-{branch}.sql and install.sql
- PHP files are include-files for updater script and must have
defined('COT_UPDATE') or die(); check on top

Please use install.php updater to apply recent SQL/config/PHP
updates. If you apply patches manually, then you need update
"revision" in sed_stats table manually with an SQL query.
That stat reflects latest revision SQL/PHP patch applied.

Updates prior to Siena are not covered by automatic updater
and should be applied using files and instructions from Genoa.

You may ask questions and make suggestions in this topic:
http://www.cotonti.com/forums.php?m=posts&q=488