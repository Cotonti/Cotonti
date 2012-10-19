<!-- BEGIN: NEWS -->

<h4 class="none">{PHP.L.News}</h4>

<!-- BEGIN: PAGE_ROW -->
<h2><a href="{PAGE_ROW_URL}">{PAGE_ROW_SHORTTITLE}</a></h2>
<p class="details">{PAGE_ROW_OWNER}, {PAGE_ROW_DATE} in {PAGE_ROW_CATPATH}</p>
<div>{PAGE_ROW_TEXT_CUT}</div>
<div class="clear">&nbsp;</div>
<!-- IF {PAGE_ROW_TEXT_IS_CUT} --><a href="{PAGE_ROW_URL}" class="more"><span>{PHP.L.ReadMore}</span></a> &nbsp;<!-- ENDIF --> 
<a href="{PAGE_ROW_URL}#com" class="comm"><span>{PAGE_ROW_COMMENTS_COUNT} {PHP.L.comments_comments}</span></a>
<div class="hr"><hr />&nbsp;</div>
<!-- END: PAGE_ROW -->

<div class="pagnav">{PAGE_PAGEPREV} {PAGE_PAGNAV} {PAGE_PAGENEXT}</div>

<!-- END: NEWS -->