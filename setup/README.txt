Since Cotonti Siena 0.9.4 updates are organized this way:
- This setup folder has subfolders for each branch (major version)
- Within each branch patches have minor version and patch number
- SQL patches are named patch_{ver}-{patch_num}.sql
- PHP patches are named patch_{ver}-{patch_num}.inc
Where {ver} is a minor version like 0.9.5 and {patch_num} is number
of the patch within that {ver} with trailing zeroes. So it is normally
the previous patch number increased by one. Here is a valid patch sequence
example:
patch_0.9.4-001.sql
patch_0.9.4-002.inc -- this is complementary patch for 0.9.4-002.sql
patch_0.9.4-002.sql
patch_0.9.4-003.sql
- SQL patch for moving from previous branch to current branch
has name patch-{previous_branch_name}.sql
- PHP patch for moving from previous branch to current branch
has name patch-{previous_branch_name}.inc
- Blank installation SQL file is called install.sql
- If you make changes in SQL, you apply them on all 3 files
(if necessary): patch_{ver}-{patch-num}.sql, patch-{branch}.sql and install.sql
- PHP files are include-files for updater script and must have
defined('COT_UPDATE') or die(); check on top

Please use install.php updater to apply recent SQL/config/PHP
updates. If you apply patches manually, then you need update
"revision" in cot_updates table manually with an SQL query.
That option reflects latest revision SQL/PHP patch applied.

Updates prior to Siena are not covered by automatic updater
and should be applied using files and instructions from Genoa.

You may ask questions and make suggestions in this topic:
http://www.cotonti.com/forums.php?m=posts&q=488