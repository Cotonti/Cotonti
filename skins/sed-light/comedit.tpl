<!-- BEGIN: MAIN -->

<!-- BEGIN: COMEDIT_TITLE -->
<div id="title">
	<a href="{COMEDIT_TITLE_URL}">{COMEDIT_TITLE}</a>
</div>
<!-- END: COMEDIT_TITLE -->


<!-- BEGIN: COMEDIT_ERROR -->
<div class="block">
	<span style="color:red;">{COMEDIT_ERROR_BODY}</span>
</div>
<!-- END: COMEDIT_ERROR -->


<!-- BEGIN: COMEDIT_FORM_EDIT -->
<div class="block">
<form id="comedit" name="comedit" action="{COMEDIT_FORM_POST}" method="post" >
<table class="cells" style="width:100%;">
  <tr>
    <td width="20%"><b>{COMEDIT_POSTER_TITLE}:</b></td>
    <td width="80%">{COMEDIT_POSTER}</td>
  </tr>
  <tr>
    <td><b>{COMEDIT_IP_TITLE}:</b></td>
    <td>{COMEDIT_IP}</td>
  </tr>
  <tr>
    <td><b>{COMEDIT_DATE_TITLE}:</b></td>
    <td>{COMEDIT_DATE}</td>
  </tr>
  <tr>
    <td colspan="2">{COMEDIT_FORM_TEXT}</td>
    </tr>
  <tr>
    <td colspan="2" class="valid"> <div align="center">
      <input type="submit" class="submit" value="{COMEDIT_FORM_UPDATE_BUTTON}">
    </div></td>
    </tr>
</table>
</form>
</div>
<!-- END: COMEDIT_FORM_EDIT -->


<!-- BEGIN: COMEDIT_EMPTY -->
<div class="block">
	<b>{GUESTBOOK_EMPTYTEXT}</b>
</div>
<!-- END: COMEDIT_EMPTY -->


<br />
<!-- END: MAIN -->