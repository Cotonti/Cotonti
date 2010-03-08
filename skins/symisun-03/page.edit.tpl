<!-- BEGIN: MAIN -->

			<div id="left" class="whitee" style="margin-right:23px;">

				<h1>{PAGEEDIT_PAGETITLE}</h1>
				<p class="details">{PAGEEDIT_SUBTITLE}</p>

				<!-- BEGIN: PAGEEDIT_ERROR -->
				<p class="error">{PAGEEDIT_ERROR_BODY}</p>
				<!-- END: PAGEEDIT_ERROR -->

				<form action="{PAGEEDIT_FORM_SEND}" method="post">

				<div id="right" style="margin:0 0 0 18px;">

					<h3>{PHP.L.Category}</h3>
					<div class="box padding15">
						{PAGEEDIT_FORM_CAT}
					</div>

					<h3>{PHP.skinlang.pageadd.adv}</h3>
					<div class="box padding15">
						<div style="width:50%; float:left"><p>{PHP.L.Extrakey}<br />{PAGEEDIT_FORM_KEY}</p>
						<p>{PHP.L.Alias}<br />{PAGEEDIT_FORM_ALIAS}</p></div>
						<!-- BEGIN: ADMIN -->
						<div style="width:50%; float:left">
						<p>{PHP.L.Parser}<br />{PAGEEDIT_FORM_TYPE}</p><p>{PHP.L.Hits}<br />{PAGEEDIT_FORM_PAGECOUNT}</p></div>
						<p>{PHP.L.Owner}<br />{PAGEEDIT_FORM_OWNERID}</p>
						<!-- END: ADMIN -->
					</div>

					<h3>{PHP.skinlang.pageadd.dates}</h3>
					<div class="box padding15" style="padding-right:5px">
						<p>{PHP.L.Date}<br />{PAGEEDIT_FORM_DATE}</p>
						<p>{PHP.L.Begin}<br />{PAGEEDIT_FORM_BEGIN}</p>
						<p>{PHP.L.Expire}<br />{PAGEEDIT_FORM_EXPIRE}</p>
					</div>

					<h3>{PHP.L.Publish}</h3>
					<div class="box padding15">
						<!-- IF !{PHP.usr.isadmin} -->
						<p class="red">{PHP.skinlang.pageadd.Formhint}</p>
						<!-- ENDIF -->

						<!-- IF {PHP.usr_can_publish} -->
						<input name="rpublish" type="submit" class="submit" value="{PHP.L.Update}" onclick="this.value='OK';return true" />
						<input type="submit" value="{PHP.L.Putinvalidationqueue}" class="undo" />
						<!-- ELSE -->
						<input type="submit" value="{PHP.L.Submit}" class="submit" />
						<!-- ENDIF -->
					</div>

					<h3>{PHP.skinlang.pageedit.del}</h3>
					<div class="box padding15">
						<p><strong class="red">{PHP.skinlang.pageedit.Deletethispage}</strong> &nbsp; {PAGEEDIT_FORM_DELETE}</p>
					</div>

					&nbsp;

				</div>


				<fieldset>
					<legend>{PHP.skinlang.pageadd.basic}</legend>
					<div><label>{PHP.L.Title}</label>{PAGEEDIT_FORM_TITLE} &nbsp; 
					<span class="hint">{PHP.skinlang.pageedit.Pageid}: #{PAGEEDIT_FORM_ID}</span></div>
					<div><label>{PHP.L.Description}</label>{PAGEEDIT_FORM_DESC}</div>
					<!-- BEGIN: TAGS -->
					<div><label>{PAGEEDIT_TOP_TAGS}</label>{PAGEEDIT_FORM_TAGS} &nbsp; 
					<span class="hint">{PAGEEDIT_TOP_TAGS_HINT}</span></div>
					<!-- END: TAGS -->
				</fieldset>
				<fieldset>
					<legend>{PHP.L.Text}</legend>
					<div style="padding:0 10px; margin-top:-15px" class="pageadd">
						{PAGEEDIT_FORM_TEXT} &nbsp; &nbsp; 
						{PAGEEDIT_FORM_PFS_TEXT_USER} &nbsp; {PAGEEDIT_FORM_PFS_TEXT_SITE}
					</div>
				</fieldset>
				<fieldset>
					<legend>{PHP.skinlang.pageadd.down}</legend>
					<div><label>{PHP.skinlang.pageadd.File}</label>{PAGEEDIT_FORM_FILE} &nbsp; 
					<span class="hint">{PHP.skinlang.pageadd.Filehint}</span></div>
					<div><label>{PHP.L.URL}</label>{PAGEEDIT_FORM_URL} &nbsp; 
					{PAGEEDIT_FORM_PFS_URL_USER} &nbsp; {PAGEEDIT_FORM_PFS_URL_SITE} &nbsp; 
					<span class="hint">{PHP.skinlang.pageadd.URLhint}</span></div>
					<div><label>{PHP.skinlang.pageadd.Filesize}</label>{PAGEEDIT_FORM_SIZE} &nbsp; 
					<span class="hint">{PHP.skinlang.pageadd.Filesizehint}</span></div>
					<div><label>{PHP.skinlang.pageedit.Filehitcount}</label>{PAGEEDIT_FORM_FILECOUNT} &nbsp; 
					<span class="hint">{PHP.skinlang.pageedit.Filehitcounthint}</span></div>
				</fieldset>

				</form>

			</div>

		</div>

	</div>

	<br class="clear" />

<!-- END: MAIN -->