<!-- BEGIN: FOOTER -->
</div>

<div id="bott">
	<div id="footer">
    	<!-- {FOOTER_CREATIONTIME} {FOOTER_SQLSTATISTICS} {FOOTER_DEVMODE} -->
        <div class="padding10" style="padding-top:92px">
   	    	<div id="footinfo" class="centerall">
           		Place secondary navigation here<br />
				<a href="#one">Link One</a> -
				<a href="#two">Link Two</a> -
				<a href="#three">Link Three</a> -
				<a href="#four">Link Four</a>

				<div id="footsearch">
					<form action="{PHP|cot_url('plug','e=search')}" method="post">
					<p>
					<input type="text" name="rsq" value="type term..." onfocus="if (this.value == 'type term...') this.value = '';" class="sq" maxlength="40" />
					<input value="{PHP.L.Search|strtoupper}" type="submit" class="sb" />
					<a href="{PHP|cot_url('plug','e=search')}" class="adv"><span>{PHP.themelang.pageadd.adv}</span></a>
					</p>
					</form>
				</div>
        	</div>
            <div class="footer_divider"></div>
        	<div class="footer_box colright">
            	<h6><span class="icon_donate">DONATE</span></h6>
				<p class="padding5">Do you enjoy using Cotonti and wish to help out?</p>
				<ul>
				<li><a href="http://www.cotonti.com/donate">Donate today</a></li>
				</ul>
            </div>
            <div class="footer_box colright">
            	<h6><span class="icon_contact">CONTACT</span></h6>
				<ul>
				<li><a href="#email">Direct Email</a></li>
				<li>Tel: (+30) 010101010</li>
				<li><a href="#form">Contact form</a></li>
				</ul>
            </div>
            <div class="footer_box colright">
            	<h6><span class="icon_download">DOWNLOAD</span></h6>
				<ul>
				<li>Latest build: <a href="#siena">Siena</a></li>
				<li><a href="#themes">Themes</a></li>
				<li><a href="#extensions">Extensions</a></li>
				<li><a href="#addons">Add-ons</a></li>
				<li><a href="#lang">Language Packs</a></li>
				</ul>
            </div>
        </div>
        <div id="top"><a href="{PHP.out.uri}#topofpage">{PHP.themelang.footer.top}</a></div>
        <div id="bot">
        	Copyright &copy; 2009 SymiSun* 03, Web Design by <a href="http://symisun.com" title="We digitalize your ambitions">SymiSun<span class="orange">*</span></a>
            <br class="none" />
            <span style="padding:0 95px 0 95px">{PHP.cfg.menu2}</span><br class="none" />
            {PHP.cfg.freetext4}
        </div>
        {PHP.cfg.freetext9}
		<a href="http://www.cotonti.com" title="Cotonti Content Management System" id="powered">POWERED BY COTONTI</a>

    </div>
</div>

{FOOTER_RC}
</body>
</html>
<!-- END: FOOTER -->
