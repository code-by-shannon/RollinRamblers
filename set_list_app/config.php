<?php
// config.php

$host = $_SERVER['HTTP_HOST'] ?? 'cli';
$isLocal = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');

// Base URLs
$LOCAL_BASE = 'http://localhost';
// Auto-derive prod base from current host so you don't edit it on SiteGround:
$PROD_BASE  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $host;

// API folder paths
$LOCAL_PREFIX = '/rollinramblers/set_list_app/setlist_api';
$PROD_PREFIX  = '/setlist_api'; // if your prod API lives elsewhere, change this ONE value

$config = [
  'env'        => $isLocal ? 'dev' : 'prod',
  'base_url'   => $isLocal ? $LOCAL_BASE : $PROD_BASE,
  'api_prefix' => $isLocal ? $LOCAL_PREFIX : $PROD_PREFIX,
  'debug'      => $isLocal,
];

// (Optional) DB config you can reuse elsewhere later:
$config['db'] = $isLocal
  ? ['host'=>'127.0.0.1','user'=>'root','pass'=>'','name'=>'rollin_ramblers_db']
  : ['host'=>'localhost','user'=>'YOUR_SG_USER','pass'=>'YOUR_SG_PASS','name'=>'YOUR_SG_DB'];
