<!-- BEGIN: MAIN -->

			<div id="left" class="whitee" style="margin-right:23px;">
        	
				<h1>{PAGEADD_SUBTITLE}</h1>

				<!-- BEGIN: PAGEADD_ERROR -->
				<p class="error">{PAGEADD_ERROR_BODY}</p>
				<!-- END: PAGEADD_ERROR -->

				<form action="{PAGEADD_FORM_SEND}" method="post">

				<div id="right" style="margin:0 0 0 18px;">

					<h3>{PHP.L.Category}</h3>
					<div class="box padding15">
						{PAGEADD_FORM_CAT}
					</div>

					<h3>{PHP.skinlang.pageadd.adv}</h3>
					<div class="box padding15">
						<p>{PHP.L.Extrakey}<br />{PAGEADD_FORM_KEY}</p>
						<p>{PHP.L.Alias}<br />{PAGEADD_FORM_ALIAS}</p>
					</div>

					<h3>{PHP.skinlang.pageadd.dates}</h3>
					<div class="box padding15" style="padding-right:5px">
						<p>{PHP.L.Begin}<br />{PAGEADD_FORM_BEGIN}</p>
						<p>{PHP.L.Expire}<br />{PAGEADD_FORM_EXPIRE}</p>
					</div>

					<h3>{PHP.L.Publish}</h3>
					<div class="box padding15">
						<!-- IF !{PHP.usr.isadmin} -->
						<p class="red">{PHP.skinlang.pageadd.Formhint}</p>
						<!-- ENDIF -->

						<!-- IF {PHP.usr_can_publish} -->
						<input name="rpublish" type="submit" class="submit" value="{PHP.L.Publish}" onclick="this.value='OK';return true" />
						<input type="submit" value="{PHP.L.Putinvalidationqueue}" class="undo" />
						<!-- ELSE -->
						<input type="submit" value="{PHP.L.Submit}" class="submit" />
						<!-- ENDIF -->
					</div>

					&nbsp;

				</div>

				<fieldset>
				<legend>{PHP.skinlang.pageadd.basic}</legend>
				<div><label>{PHP.L.Title}</label>{PAGEADD_FORM_TITLE}</div>
				<div><label>{PHP.L.Description}</label>{PAGEADD_FORM_DESC}</div>
				<!-- BEGIN: TAGS -->
				<div>
					<label>{PAGEADD_TOP_TAGS}</label>
					{PAGEADD_FORM_TAGS} &nbsp; <span class="hint">{PAGEADD_TOP_TAGS_HINT}</span>
				</div>
				<!-- END: TAGS -->
				</fieldset>

				<fieldset>
				<legend>{PHP.L.Text}</legend>
				<div class="pageadd" style="margin-top:-20px">
					{PAGEADD_FORM_TEXT}
					 &nbsp; &nbsp; {PAGEADD_FORM_PFS_TEXT_USER} &nbsp; {PAGEADD_FORM_PFS_TEXT_SITE}
				</div>
				</fieldset>

				<fieldset>
				<legend>{PHP.skinlang.pageadd.down}</legend>
				<div>
					<label>{PHP.skinlang.pageadd.File}</label>
					{PAGEADD_FORM_FILE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.Filehint}</span>
				</div>
				<div>
					<label>{PHP.L.URL}</label>
					{PAGEADD_FORM_URL} &nbsp; {PAGEADD_FORM_PFS_URL_USER} &nbsp; {PAGEADD_FORM_PFS_URL_SITE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.URLhint}</span>
				</div>
				<div>
					<label>{PHP.skinlang.pageadd.Filesize}</label>
					{PAGEADD_FORM_SIZE} &nbsp; <span class="hint">{PHP.skinlang.pageadd.Filesizehint}</span>
				</div>
				</fieldset>
				
				</form>

			</div>

		</div>

	</div>

	<br class="clear" />

<!-- END: MAIN -->