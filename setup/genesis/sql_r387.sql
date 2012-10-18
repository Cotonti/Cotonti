/* r387 Structure page count fix for news */ 
UPDATE sed_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='news';