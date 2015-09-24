<?php
    include "Forums/Forum_Dependencies.php";
?>

<div id="forum_parent" class="units-row unit-100">
    <div id="forum_input">
        <textarea id="ForumSend" rows="2"></textarea>
        <button class="btn-blue" style="width:100%" onclick="PostF()">Send</button>
    </div>
    <button id="btn_new_post" class="btn-blue" style="width:100%" onclick="NewPost()">New Post</button>
    <div id="chat-wrap">
        <div id="forum-area">
        </div>
    </div>
</div>

<script type="text/javascript">
	init(<?= json_encode($cm->id) ?>, <?= json_encode($USER->id) ?>);
</script>

