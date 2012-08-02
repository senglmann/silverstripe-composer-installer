<?php
/**
 * @package silverstripe-composer-installer
 */

namespace SilverStripe\ComposerInstaller;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

/**
 * A custom composer installer for installing the silverstripe 
 * framework to the base folder.
 *
 * @package silverstripe-composer-installer
 */
class FrameworkInstaller extends LibraryInstaller {

	public function supports($type) {
		return $type == 'silverstripe-framework';
	}

	public function getInstallPath(PackageInterface $package) {
		return "framework";
	}

}
