<!-- BEGIN: MAIN -->
{PHP.cfg.doctype}
<html>
<head>
{POPUP_METAS}
<base href="{PHP.cfg.mainurl}/" />
{POPUP_JAVASCRIPT}
<script type="text/javascript">
//<![CDATA[
function add(text) {
	insertText(document, "{POPUP_C1}", "{POPUP_C2}", text);
}
//]]>
</script>
<link href="skins/{PHP.skin}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head>
<body>

{POPUP_BODY}

</body>
</html>
<!-- END: MAIN -->