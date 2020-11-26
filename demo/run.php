<?php

use jooxMusic\Api;

include_once __DIR__.'/../src/Api.php';
include_once __DIR__.'/../vendor/autoload.php';


$api = new Api();
// $ret = $api->getSingerInfo('u6dpDVwY9QvUryLL7LDxyw==');
// $ret = $api->getSingerAlbums('u6dpDVwY9QvUryLL7LDxyw==');
// $ret = $api->getSingerSongs('u6dpDVwY9QvUryLL7LDxyw==', 1, 10);
// $ret = $api->getAlbumSongs('fD4yj7c_3QCBgmA5oICoog==');
// $ret = $api->getSongInfo('R9593pReubV2vpPFqGAFiQ==');
// $ret = $api->getChartSongs(1);
$ret = $api->getSingersRankInfo('u6dpDVwY9QvUryLL7LDxyw==');
echo json_encode($ret, JSON_UNESCAPED_UNICODE)."\n";