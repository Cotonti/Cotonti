<!-- BEGIN: MAIN -->

<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

<div id="content">
  <div class="padding20">
    <div id="left">
      <h1>{PHP.L.Private_Messages}</h1>
      <div class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('users')}">{PHP.L.Users}</a> <a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.usr.name}</a> {PM_PAGETITLE} </div>
      <p class="details">{PM_SUBTITLE}</p>

	  
			<form action="{PM_FORM_UPDATE}" method="post" name="update" id="update" class="ajax">
				<table class="cells">
					<tr>
						<td class="coltop width5">
							<!-- IF {PHP.cfg.jquery} --><input class="checkbox" type="checkbox" value="{PHP.themelang.pm.Selectall}/{PHP.themelang.pm.Unselectall}" onclick="$('.checkbox').attr('checked', this.checked);" /><!-- ENDIF -->
						</td>
						<td class="coltop width5">{PHP.L.Status}</td>
						<td class="coltop width5">
							<div class="pm-star pm-star-readonly">
								<a href="#" title ="{PHP.L.pm_starred}"> &nbsp; </a>
							</div>
						</td>
						<td class="coltop width40">{PHP.L.Subject}</td>
						<td class="coltop width15">{PM_SENT_TYPE}</td>
						<td class="coltop width15">{PHP.L.Date}</td>
						<td class="coltop width15">{PHP.L.Action}</td>
					</tr>
					<!-- BEGIN: PM_ROW -->
					<tr>
						<td class="centerall {PM_ROW_ODDEVEN}"><input type="checkbox" class="checkbox" name="msg[{PM_ROW_ID}]" /></td>
						<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_STATUS}</td>
						<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_STAR}</td>
						<td class="{PM_ROW_ODDEVEN}">
							<p class="strong">{PM_ROW_TITLE}</p>
							<p class="small">{PM_ROW_DESC}</p>
						</td>
						<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_USER_NAME}</td>
						<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_DATE}</td>
						<td class="centerall {PM_ROW_ODDEVEN}">{PM_ROW_ICON_EDIT} {PM_ROW_ICON_DELETE}</td>
					</tr>
					<!-- END: PM_ROW -->
					<!-- BEGIN: PM_ROW_EMPTY -->
					<tr>
						<td class="centerall" colspan="7">{PHP.L.None}</td>
					</tr>
					<!-- END: PM_ROW_EMPTY -->
				</table>
				<!-- IF {PHP.jj} > 0 -->
				<p class="paging">
					<span class="strong">{PHP.L.pm_selected}:</span>
					<select name="action" size="1">
						<option value="delete" >{PHP.L.Delete}</option>
						<option value="star" selected="selected">{PHP.L.pm_putinstarred}</option>
					</select>
					<button type="submit" name="delete">{PHP.L.Confirm}</button>
				</p>
				<p class="paging">{PM_PAGEPREV}{PM_PAGES}{PM_PAGENEXT}</p>
				<!-- ENDIF -->
			</form>  
	  

    </div>
	
    <div id="right">
      <h3 style="color:#000">{PHP.L.hea_youareloggedas} {PHP.usr.name}</h3>
      <h3><a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.L.View} {PHP.L.Profile}</a></h3>
      <h3><a href="{PHP|cot_url('users','m=profile')}">{PHP.L.Update} {PHP.L.Profile}</a></h3>
      <h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
      <div class="padding15 admin" style="padding-bottom:0">
        <ul>
          <li>{PM_INBOX}</li>
          <!-- <li>{PM_ARCHIVES}</li> does it still exist?-->
          <li>{PM_SENTBOX}</li>
          <li>{PM_SENDNEWPM}</li>
		  <li>{PHP.L.Filter}: {PM_FILTER_UNREAD}, {PM_FILTER_STARRED}, {PM_FILTER_ALL} </li>
        </ul>
      </div>
      <h3><a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></h3>
      <h3><a href="{PHP|cot_url('users')}">{PHP.L.Users}</a></h3>
      &nbsp; </div>
  </div>
</div>
<br class="clear" />

	<!-- IF {PHP.cfg.jquery} -->
	<script type="text/javascript" src="{PHP.cfg.modules_dir}/pm/js/pm.js"></script>
	<!-- ENDIF -->

<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->

<!-- END: MAIN -->