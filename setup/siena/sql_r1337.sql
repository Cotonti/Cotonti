/* r1337 prefix change */
UPDATE `cot_bbcode` SET `bbc_replacement` = 'return cot_obfuscate(''<a href="mailto:''.$input[1].''">''.$input[2].''</a>'');'
	WHERE `bbc_name` = 'email';
UPDATE `cot_bbcode` SET `bbc_replacement` = 'return ''<pre class="code">''.cot_bbcode_cdata($input[1]).''</pre>'';'
	WHERE `bbc_name` = 'code';