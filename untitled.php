<?php
$str = "SELECT DISTINCT t.*,n.title AS forum_title,u.user_group_id,u.display_style_group_id FROM xf_post p LEFT JOIN xf_thread t using (thread_id) LEFT JOIN xf_node n using(node_id) LEFT JOIN xf_user u ON t.last_post_user_id=u.user_id WHERE p.user_id='1' AND t.reply_count>0 AND `discussion_state`='visible' AND `message_state`='visible' ORDER BY p.post_date DESC LIMIT 15";

$s = preg_match("/xf_userdf/", $str);

var_dump($s);