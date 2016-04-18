<?php
/**
 * This overrides some values for the animals without having to configure it
 *
 * This file is added to the protected cascade for animals only.
 * You should not edit it!
 */
global $FARMCORE;
$conf['savedir'] = $FARMCORE->getAnimalDataDir();
$conf['basedir'] = $FARMCORE->getAnimalBaseDir();
$conf['upgradecheck'] = 0;


