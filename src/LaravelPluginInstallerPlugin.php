<?php

namespace Maneash\LaravelPluginInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class LaravelPluginInstallerPlugin implements PluginInterface
{

    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new LaravelPluginInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

}
