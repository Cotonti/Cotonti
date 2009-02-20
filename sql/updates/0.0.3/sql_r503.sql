/* Fix for #208, a bug in img bbcode */
UPDATE sed_bbcode SET bbc_pattern = '\\[img=((?:http://|https://|ftp://)?[^\\]\"\';:\\?]+\\.(?:jpg|jpeg|gif|png))\\]((?:http://|https://|ftp://)?[^\\]\"\';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]' WHERE bbc_name = 'img' AND bbc_replacement = '<a href="$1"><img src="$2" alt="" /></a>';
