<?php
    include "Forums/Forum_Dependencies.php";
?>

<div id="forum_title_section">
	<h2 id="forum_title">
	StreamLine Lecture Forum
	</h2>
</div>

<div id="forum-parent" class="units-row unit-100">
    <div id="chat-wrap">
        <center>
            <img id="forum-loading" src="pix/loading.gif"/>
            <p id="forum-no-history" class="forum-hidden">No forum history</p>
        </center>
        <div id="forum-area" class="forum-hidden"></div>
    </div>
</div>

<script type="text/javascript">
	init(<?= json_encode($cm->id) ?>, <?= json_encode(bin2hex($USER->id)) ?>, <?= json_encode($USER->id) ?>);
</script>
