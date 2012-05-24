<?php
/**
 * @package silverstripe-composer-installer
 */

namespace SilverStripe\Framework\ComposerInstaller;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

/**
 * A custom composer installer for installing silverstripe modules, installs
 * them to the base folder.
 *
 * @package silverstripe-composer-installer
 */
class ModuleInstaller extends LibraryInstaller {

	public function supports($type) {
		return $type == 'silverstripe-module';
	}

	public function getInstallPath(PackageInterface $package) {
		return array_pop(explode('/', $package->getName()));
	}

}
