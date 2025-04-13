<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
    <a href="{COMMENTS_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<!-- IF {TOTAL_ENTRIES} === 0 OR {ENTRIES_ON_CURRENT_PAGE} === 0 -->
<div class="block">
    <div class="wrapper">
        <div class="text-center text-secondary margintop10">{PHP.L.comments_noYet}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- BEGIN: COMMENTS_ROW -->
<div id="com{COMMENTS_ROW_ID}" class="comment-row card mb-3">
    <div class="card-body d-flex gap-3 align-items-start">
        {COMMENTS_ROW_AUTHOR_AVATAR}
        <div class="d-flex flex-column flex-grow-1">
            <div class="flex-grow-1">
                <h6 class="mb-1">#{COMMENTS_ROW_ID}. {COMMENTS_ROW_TITLE}</h6>
                <div class="mb-2">
                    <!-- IF {COMMENTS_ROW_AUTHOR_DETAILS_URL} --><a href="{COMMENTS_ROW_AUTHOR_DETAILS_URL}"><!-- ENDIF -->
                        {COMMENTS_ROW_AUTHOR_FULL_NAME}
                        <!-- IF {COMMENTS_ROW_AUTHOR_DETAILS_URL} --></a><!-- ENDIF -->
                    <small class="text-muted">• {COMMENTS_ROW_DATE}</small>
                </div>
                <div class="comment-text closed mb-2">{COMMENTS_ROW_TEXT}</div>
            </div>
            <div class="d-flex">
                <div class="flex-grow-1">
                    <!-- IF {COMMENTS_ROW_URL} -->
                        <a href="{COMMENTS_ROW_URL}" class="text-decoration-none me-2">{PHP.L.Open}</a>
                    <!-- ENDIF -->
                    <a href="{COMMENTS_ROW_EDIT_URL}" class="text-decoration-none me-2">{PHP.L.Edit}</a>
                    <a href="{COMMENTS_ROW_DELETE_URL}" class="confirmLink text-decoration-none text-danger">{PHP.L.Delete}</a>
                </div>
                <div class="text-end">
                    <!-- IF {COMMENTS_ROW_AUTHOR_IP} -->
                    <small>{PHP.L.Ip}: {COMMENTS_ROW_AUTHOR_IP}</small>
                    <!-- ENDIF -->
                    <a href="#" class="comment-text-toggle ms-2" style="display: none" title="{PHP.L.Unfold}">{PHP.R.icon_down}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: COMMENTS_ROW -->

<!-- IF {TOTAL_ENTRIES} > 0 -->
<p class="paging">
    {PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
    <span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
</p>
<!-- ENDIF -->

<style>
    .comment-text {
        max-height: 1200px;
        transition: height ease-in-out 0.2s, max-height ease-in-out 0.2s;
        height: auto;
        overflow-y: hidden;
    }
    .comment-text.closed {
        max-height: 110px;
    }
    .comment-text p:last-child {
        margin-bottom: 0;
    }
    .comment-text-toggle {

    }
</style>
<script>
    setTimeout(function () {
        const toggles = document.querySelectorAll('.comment-text-toggle');
        for (let elem of toggles) {
            const textContainer = elem.closest('.comment-row').querySelector('.comment-text');
            if (textContainer.clientHeight < 110) {
                elem.remove();
                continue;
            }

            elem.style.display = '';

            elem.addEventListener('click', (e) => {
                e.preventDefault();
                const target = e.target.closest('.comment-text-toggle');
                if (textContainer.classList.contains('closed')) {
                    target.innerHTML = '{PHP.R.icon_up_active}';
                    target.title = '{PHP.L.Fold}'
                } else {
                    target.innerHTML = '{PHP.R.icon_down}';
                    target.title = '{PHP.L.Unfold}'
                }
                textContainer.classList.toggle('closed');
            });
        }
    }, 500);
</script>
<!-- END: MAIN -->