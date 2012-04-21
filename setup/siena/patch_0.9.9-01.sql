/* 0.9.9-01 move shield back to the core */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','security','21','shieldenabled',3,'0','0','',''),	  	
('core','security','22','shieldtadjust',2,'100','100','10,25,50,75,100,125,150,200,300,400,600,800',''), 	
('core','security','23','shieldzhammer',2,'25','25','5,10,15,20,25,30,40,50,100','');