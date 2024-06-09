<!-- BEGIN: MAIN -->
<!DOCTYPE html>
<html lang="{PHP.cfg.defaultlang}">
<head>
    <base href="{PHP.cfg.mainurl}/" />
    <script type="text/javascript">
        //<![CDATA[
        function add(text) {
            insertText(document, "{POPUP_C2}", text);
        }
        //]]>
    </script>
    <link href="{PHP.cfg.themes_dir}/{PHP.theme}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
</head>
<body>
{CONTENT}
</body>
</html>
<!-- END: MAIN -->