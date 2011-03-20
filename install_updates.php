<?php

function SEARCHRANK_update_ConfValues_1_0_0()
{
    global $_CONF, $_SR_DEFAULT;

    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $me = 'searchrank';

    $c = config::get_instance();
    $c->add('showstats', false, 'select', 0, 0, 1, 5, true, $me, 0);
    $c->add('itemsperpage', 20, 'text', 0, 0, null, 60, true, $me, 0);
    $c->add('blockorder', 1, 'text', 0, 0, null, 25, true, $me, 0);
    $c->add('rootonly', false, 'select', 0, 0, 1, 20, true, $me, 0);
}

?>
