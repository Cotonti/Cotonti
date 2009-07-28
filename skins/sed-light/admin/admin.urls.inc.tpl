<!-- BEGIN: URLS -->
<div id="{ADMIN_URLS_AJAX_OPENDIVID}">
    <!-- IF {PHP.is_adminwarnings} -->
    <div class="error">{ADMIN_URLS_ADMINWARNINGS}</div>
    <!-- ENDIF -->
<!-- BEGIN: HTA -->
    <h4>{PHP.L.adm_urls_your} <em>{ADMIN_URLS_CONF_NAME}</em></h4>
    <pre class="code">{ADMIN_URLS_HTA}</pre>
    <!-- END: HTA -->
    <h4>{PHP.L.adm_urls_rules}</h4>
    <script type="text/javascript" src="js/jquery.tablednd.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#rules").tableDnD({});
            $("#rule_new").hide();
            $("#add_rule").show();
        });
        var ruleCount = 0;
        function addRule()
        {
            var object = $('#rule_new').clone().attr("id", 'rule_'+ruleCount).show();
            $('#rules_top').after(object);
            $("#rules").tableDnDUpdate();
            ruleCount++;
            return false;
        }
    </script>
    <style type="text/css">tr.tDnD_whileDrag td{background-color:yellow;}</style>
    <form name="add_url" id="add_url" action="{ADMIN_URLS_FORM_URL}" method="post"{ADMIN_URLS_FORM_URL_AJAX}>
          <table id="rules" class="cells">
            <tr id="rules_top" class="nodrop nodrag">
                <td class="coltop">{PHP.L.adm_urls_area}</td>
                <td class="coltop">{PHP.L.adm_urls_parameters}</td>
                <td class="coltop">{PHP.L.adm_urls_format}</td>
                <td class="coltop">{PHP.L.Delete}</td>
            </tr>
            <!-- BEGIN: ROW -->
            <tr id="rule_{ADMIN_URLS_ROW_I}">
                <td>
                    <select name="area[]">
                        <!-- BEGIN: AREABOX2 -->
                        <option{ADMIN_URLS_AREABOX_SELECTED}>{ADMIN_URLS_AREABOX_ITEM}</option>
                        <!-- END: AREABOX2 -->
                    </select>
                </td>
                <td><input type="text" name="params[]" value="{ADMIN_URLS_ROW_PARTS1}" /></td>
                <td><input type="text" name="format[]" value="{ADMIN_URLS_ROW_PARTS2}" /></td>
                <td><a href="#" onclick="$(this).parent().parent().remove();return false;">[X]</a></td>
            </tr>
            <!-- END: ROW -->


            <tr id="rule_new">
                <td>
                    <select name="area[]">
                        <!-- BEGIN: AREABOX --><option{ADMIN_URLS_AREABOX_SELECTED}>{ADMIN_URLS_AREABOX_ITEM}</option><!-- END: AREABOX -->
                    </select>
                </td>
                <td><input type="text" name="params[]" value="*" /></td>
                <td><input type="text" name="format[]" value="" /></td>
                <td><a href="#" id="delete_rule" onclick="$(this).parent().parent().remove(); return false;">[X]</a></td>
            </tr>

            <tr id="add_rule" style="display:none;" class="nodrop nodrag">
                <td colspan="4">
                    <script type="text/javascript">
                        ruleCount = {ADMIN_URLS_II};
                    </script>
                    <a href="#" onclick="return addRule()"><strong>{PHP.L.adm_urls_new}</strong></a>
                </td>
            </tr>
        </table>
        <!-- IF {PHP.htaccess} -->
        <br /><input type="checkbox" name="htaccess" /> {PHP.L.adm_urls_htaccess}
        <!-- ENDIF -->
        <br />
        <input type="submit" value="{PHP.L.adm_urls_save}" />
    </form>
</div>
<!-- END: URLS -->