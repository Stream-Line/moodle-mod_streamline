<?php
    include "Forums/Forum_Dependencies.php";
?>

<div id="forum_parent" class="units-row unit-100">
    <div id="forum-input-main" class="forum-post">
        <div id="forum-user-dp-main" class="post-dp">
		</div>
        <div class="forum-input">
            <textarea id="forum-textarea-main"></textarea>
            </div>
        <div id="forum-input-btns-main" class="input-buttons hidden">
            <button id="btn-cancel-main">Cancel</button>
            <button id="btn-send-main" class="btn-blue">Send</button>
        </div>
    </div>
    <div id="chat-wrap">
        <div id="forum-area">
            <div id="forum-input-reply" class="forum-post post-reply hidden">
                <div id="forum-user-dp-reply" class="post-dp">
        		</div>
                <div class="forum-input">
                    <textarea id="forum-textarea-reply"></textarea>
                    </div>
                <div id="forum-input-btns-reply" class="input-buttons">
                    <button id="btn-cancel-reply">Cancel</button>
                    <button id="btn-send-reply" class="btn-blue">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	init(<?= json_encode($cm->id) ?>, <?= json_encode(bin2hex($USER->id)) ?>, <?= json_encode($USER->id) ?>);
</script>

