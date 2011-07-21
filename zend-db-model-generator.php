<?php

if (!is_file(dirname(__FILE__).'/config/config.php')) {
    die('please copy config/config.php-default to config/config.php and modify.');
}

define('VERSION', '0.5RC1');
define('AUTHOR', 'Kfir Ozer <kfirufk@gmail.com>');

require_once('class/MakeDbTable.php');
require_once('class/ArgvParser.php');
require_once('config/config.php');

if (!ini_get('short_open_tag')) {
    die('please enable short_open_tag directive in php.ini');
}

if (!ini_get('register_argc_argv')) {
    die('please enable register_argc_argv directive in php.ini');
}

$parser     = new ArgvParser($argv, AUTHOR, VERSION);
$params     = $parser->checkParams();
$namespace  = $config['namespace.default'];

if (sizeof($params['--namespace']) == 1) {
    $namespace = $params['--namespace'][0];
}

$dbname = $params['--database'][0];
$cls    = new MakeDbTable($config, $dbname, $namespace);
$tables = array();

if ($params['--all-tables'] OR sizeof($params['--tables-regex']) > 0) {
    $tables = $cls->getTablesNamesFromDb();
}

$tables = $parser->compileListOfTables($tables, $params);
$dir    = '';

if (sizeof($params['--location']) == 1) {
    $cls->setLocation($params['--location'][0]);
    $dir = $params['--location'][0].DIRECTORY_SEPARATOR.'Generated'.DIRECTORY_SEPARATOR.'DbTable';
} else {
    $cls->setLocation($namespace);
    $dir = $namespace.DIRECTORY_SEPARATOR.'Generated'.DIRECTORY_SEPARATOR.'DbTable';
}

if (sizeof($tables) == 0) {
    die("error: please provide at least one table to parse.\n");
}

if (!is_dir($dir)) {
    if (!@mkdir($dir, 0777, true)) {
        die("error: could not create directory ".$dir."\nFile: ".__FILE__."\nLine: ".__LINE__."\n");
    }
} elseif (!is_writable($dir)) {
    if(!chmod($dir, '0777')) {
        die("error: ".$dir." not writeable\nFile: ".__FILE__."\nLine: ".__LINE__."\n");
    }
}

foreach ($tables AS $table) {
    $cls->setTableName($table);
    $cls->parseTable();
    $cls->doItAll();
}

$cls->createMainModelFiles();
echo "Model generation complete.\n";