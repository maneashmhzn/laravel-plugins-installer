<?php

namespace Maneash\LaravelPluginInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class LaravelPluginInstaller extends LibraryInstaller
{
    const DEFAULT_ROOT = "Plugins";

    /**
     * Get the fully-qualified install path
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->getBaseInstallationPath() . '/' . $this->getPluginName($package);
    }

    /**
     * Get the base path that the plugin should be installed into.
     * Defaults to Plugins/ and can be overridden in the plugin's composer.json.
     * @return string
     */
    protected function getBaseInstallationPath()
    {
        if (!$this->composer || !$this->composer->getPackage()) {
            return self::DEFAULT_ROOT;
        }

        $extra = $this->composer->getPackage()->getExtra();

        if (!$extra || empty($extra['plugin-dir'])) {
            return self::DEFAULT_ROOT;
        }

        return $extra['plugin-dir'];
    }

    /**
     * Get the plugin name, i.e. "joshbrw/something-plugin" will be transformed into "Something"
     * @param PackageInterface $package
     * @return string
     * @throws \Exception
     */
    protected function getPluginName(PackageInterface $package)
    {
        $name = $package->getPrettyName();
        $split = explode("/", $name);

        if (count($split) !== 2) {
            throw new \Exception($this->usage());
        }

        $splitNameToUse = explode("-", $split[1]);

        if (count($splitNameToUse) < 2) {
            throw new \Exception($this->usage());
        }

        if (array_pop($splitNameToUse) !== 'plugin') {
            throw new \Exception($this->usage());
        }

        return implode('',array_map('ucfirst', $splitNameToUse));
    }

    /**
     * Get the usage instructions
     * @return string
     */
    protected function usage()
    {
        return "Ensure your package's name is in the format <vendor>/<name>-<plugin>";
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'laravel-plugin' === $packageType;
    }
}
