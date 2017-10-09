<?php
//TODO
/*
 * Do the upgrade by script
 */
use Lego\Slim\App;
use Lego\Installation\Library\Helper;
use Lego\Installation\Library\RunnerInterface;
/**
 * This script is using to running upgrading or downgrade database to correct version
 * Retrist to only admin
 */
require_once __DIR__.'/../../src/init.php';
require_once 'autoload.php';
define('APP_PATH_INSTALL',APP_ROOT_PATH. 'tools'. DS . 'install'. DS);
$settings = require APP_SETTING_PATH. 'application.php';
$app = new App($settings); 
$version = isset($_GET['version']) ? $_GET['version'] : ""; 
if(empty($version))
{
    $version = isset($argv[1]) ? $argv[1] : ""; 
}
if(empty($version))
{
    die('No Version selected');
}
$helper = new Helper();
$fileScriptName = $helper->getScriptNameFromVersion($version); 

$className = $helper->getClassNameFromVersion($version,true);

if(!file_exists($fileScriptName))
{
    die('No version script found for version '. $version);
}
$runner = new $className();
if(!($runner instanceof RunnerInterface))
{
    die('Invalid runner '. $version);
}
?>
<html>
	<head></head>
	<body>
		<div class="basic-info">
    		<h1>App Info</h1>
    		<p>Current Version: <?php echo $app->getVersion()?></p>
    		<p>Release Date: <?php echo $app->getReleaseDate()?></p>
		</div>
		<div class="release-note">
			<h1>Runner Info</h1>
			<div>
				<p>Runner Version: <?php echo $runner->getVersion();?></p>
				<p>Release Date: <?php echo $runner->getReleaseDate();?></p>
			</div>
			<div>
				<?php $runner->getInfo();?>
			</div>
			<div>
				<form method="POST" action="">
					<input type="submit" class="button" value="Process" />
				</form>
			</div>
		</div>
	</body>
</html>