/* r1169 a fix for config options type */
UPDATE `cot_config` SET `config_type` = 1
  WHERE `config_name` IN ('th_x', 'th_y', 'th_border', 'th_colorbg', 'th_colortext', 'av_maxsize', 'av_maxx',
    'av_maxy', 'usertextmax', 'sig_maxsize', 'sig_maxx', 'sig_maxy', 'ph_maxsize', 'ph_maxx', 'ph_maxy',
	'smtp_address', 'smtp_port', 'smtp_login', 'smtp_password');