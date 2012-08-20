<?php

namespace SilverStripe\ComposerInstaller;

use Composer\Script\Event;
use Composer\IO;

class SetDBConfig
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        // do stuff
		echo '###############-postUpdate-###############';
    }

    public static function postPackageInstall(Event $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        // do stuff
		echo '###############-post-Packager-Install-###############';
    }

	public static function postInstall(Event $event)
    {
        $composer = $event->getComposer();

		$io = $event->getIO();
        
		$host = $io->ask("Servername: (default: localhost): ");
		if(empty($host)) $host = 'localhost';
		
		$user = $io->ask("DB-User: (default: root)");
		if(empty($user)) $user = 'root';
		
		$database = $io->ask("Database (default: same as User):");
		if(empty($database)) $database = $user;
		
		$db_password = $io->ask("DB-Password:");
		
		$ss_user = $io->ask("SS-Admin Name (default: admin):");
		if(empty($ss_user)) $ss_user = 'admin';
		
		$ss_password = $io->ask("SS-Admin Password (default: scs-1234):");
		if(empty($ss_password)) $ss_password = 'scs-1234';
		
		$theme = $io->ask("Theme name:");
		if(empty($theme)) $theme = 'default';
		
		$io->write('creating main file structure ...');
		
		if(is_dir('themes/'.$theme)) {
			$skip_create_template = true;
			$create_dirs = array();
		}
		else {
			$skip_create_template = false;
			$create_dirs = array(
				'themes/'.$theme,
				'themes/'.$theme.'/css',
				'themes/'.$theme.'/images',
				'themes/'.$theme.'/templates',
				'themes/'.$theme.'/templates/Includes',
				'themes/'.$theme.'/templates/Layout',
			);
		}
		
		$create_dirs [] = 'mysite';
		$create_dirs [] = 'mysite/code';
		$create_dirs [] = 'assets';
		$create_dirs [] = 'assets/Uploads';
		
		foreach($create_dirs as $dir) {
			if(!is_dir($dir)) {
				$io->write('creating directory: '.$dir);	
				mkdir($dir);
			}
			else {
				$io->write($dir.' exists - skiped');
			}
		}
		
		if(file_exists('mysite/code/Page.php')) {
			$io->write('mysite/code/Page.php exists - skiped');
		} else
		\file_put_contents('mysite/code/Page.php', <<<PAGEPHP
<?php
class Page extends SiteTree {

	public static \$db = array(
	);

	public static \$has_one = array(
	);
	
}
class Page_Controller extends ContentController {

	public static \$allowed_actions = array (
	);

	public function init() {
		parent::init();
	}

}
PAGEPHP
			);
			
		if(file_exists('.htaccess')) {
			$io->write('.htaccess exists - skiped');
		} else
		\file_put_contents('.htaccess', <<<MAINHTACCESS
### SILVERSTRIPE START ###
<Files *.ss>
	Order deny,allow
	Deny from all
	Allow from 127.0.0.1
</Files>

<Files web.config>
	Order deny,allow
	Deny from all
</Files>

ErrorDocument 404 /assets/error-404.html
ErrorDocument 500 /assets/error-500.html

<IfModule mod_alias.c>
	RedirectMatch 403 /silverstripe-cache(/|$)
</IfModule>

<IfModule mod_rewrite.c>
	SetEnv HTTP_MOD_REWRITE On
	RewriteEngine On

	RewriteCond %{REQUEST_URI} ^(.*)$
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule .* framework/main.php?url=%1 [QSA]

	RewriteCond %{REQUEST_URI} ^(.*)/framework/main.php$
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule . %1/install.php? [R,L]
</IfModule>
### SILVERSTRIPE END ###
MAINHTACCESS
			);
			
		if(file_exists('mysite/.htaccess')) {
			$io->write('mysite/.htaccess - skiped');
		} else
		\file_put_contents('mysite/.htaccess', <<<HTACCESS
<FilesMatch "\.(php|php3|php4|php5|phtml|inc)$">
	Deny from all
</FilesMatch
HTACCESS
			);
			
		if(file_exists('assets/.htaccess')) {
			$io->write('assets/.htaccess exists - skiped');
		} else
		\file_put_contents('assets/.htaccess', <<<ASSETSHTACCESS
Deny from all
<FilesMatch "\.(html|HTML|htm|HTM|xhtml|XHTML|js|JS|css|CSS|bmp|BMP|png|PNG|gif|GIF|jpg|JPG|jpeg|JPEG|ico|ICO|pcx|PCX|tif|TIF|tiff|TIFF|au|AU|mid|MID|midi|MIDI|mpa|MPA|mp3|MP3|ogg|OGG|m4a|M4A|ra|RA|wma|WMA|wav|WAV|cda|CDA|avi|AVI|mpg|MPG|mpeg|MPEG|asf|ASF|wmv|WMV|m4v|M4V|mov|MOV|mkv|MKV|mp4|MP4|swf|SWF|flv|FLV|ram|RAM|rm|RM|doc|DOC|docx|DOCX|txt|TXT|rtf|RTF|xls|XLS|xlsx|XLSX|pages|PAGES|ppt|PPT|pptx|PPTX|pps|PPS|csv|CSV|cab|CAB|arj|ARJ|tar|TAR|zip|ZIP|zipx|ZIPX|sit|SIT|sitx|SITX|gz|GZ|tgz|TGZ|bz2|BZ2|ace|ACE|arc|ARC|pkg|PKG|dmg|DMG|hqx|HQX|jar|JAR|xml|XML|pdf|PDF)$">
	Allow from all
</FilesMatch>

AddHandler default-handler php phtml php3 php4 php5 inc

<IfModule mod_php5.c>
	php_flag engine off
</IfModule>
ASSETSHTACCESS
			);
			
		$io->write('writing _config.php ...');
		
		if(file_exists('mysite/_config.php')) {
			$io->write('mysite/_config.php - skiped');
		} else
		\file_put_contents('mysite/_config.php', <<<CONFIG
<?php
	global \$project;
	\$project = 'mysite';

	global \$databaseConfig;
	\$databaseConfig = array(
	        "type" => 'MySQLDatabase',
	        "server" => '$host',
	        "username" => '$user',
	        "password" => '$db_password',
	        "database" => '$database',
	        "path" => '',
	);

	MySQLDatabase::set_connection_charset('utf8');

	Director::set_environment_type('dev');
	Security::setDefaultAdmin('$ss_user', '$ss_password');

	// Set the current theme. More themes can be downloaded from
	// http://www.silverstripe.org/themes/
	SSViewer::set_theme('$theme');

	// Set the site locale
	i18n::set_locale('de_DE');

	// Enable nested URLs for this site (e.g. page/sub-page/)
	if (class_exists('SiteTree')) SiteTree::enable_nested_urls();

CONFIG
			);
		
		if($skip_create_template) {
			$io->write('tempalte exists - skiped');
		} else {
		
			$io->write('creating empty template "'.$theme.'" ...');
	
			touch('themes/'.$theme.'/css/layout.css');
			touch('themes/'.$theme.'/css/typography.css');
			touch('themes/'.$theme.'/css/editor.css');
			touch('themes/'.$theme.'/css/form.css');
			touch('themes/'.$theme.'/templates/Page.ss');
			touch('themes/'.$theme.'/templates/Layout/Page.ss');
		
		
		
			\file_put_contents('themes/'.$theme.'/css/layout.css', <<<LAYOUTCSS
* {
  margin: 0;
  padding: 0;	
}		
html {
	font-family: sans-serif;
}
body {
	font-size: 62.5%;
}
LAYOUTCSS
			);
			\file_put_contents('themes/'.$theme.'/css/typography.css', <<<TYPOCSS
.typography {
	font-size: 1.2em;
}		
TYPOCSS
			);
			\file_put_contents('themes/'.$theme.'/css/editor.css', '@import "typography.css";');
			\file_put_contents('themes/'.$theme.'/templates/Page.ss', <<<PAGESS
<!DOCTYPE html>		
<html lang="\$ContentLocale">
<head>
	<% base_tag %>
	<title><% if MetaTitle %>\$MetaTitle<% else %>\$Title<% end_if %> &raquo; \$SiteConfig.Title</title>
	<meta charset="utf-8">
	\$MetaTags(false)
	<link rel="shortcut icon" href="\$ThemeDir/images/favicon.ico" />
	<% require themedCSS(layout) %>
	<% require themedCSS(typography) %>
	<% require themedCSS(form) %>
</head>
<body>
	\$Layout
</body>
</html>		
PAGESS
			);
			\file_put_contents('themes/'.$theme.'/templates/Layout/Page.ss', <<<LAYOUTSS
<h1>\$Title</h1>
\$Content
LAYOUTSS
			);
		
		} /* end of skip create-tempalte-if-exists */
		
		$io->write('running dev/build ...');
		
		exec('sake dev/build "flush=1"');
		
		$io->write('installation done!');
    }


}