<?php

use PHPUnit\Framework\TestCase;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Joshbrw\LaravelModuleInstaller\LaravelPluginInstaller;

class LaravelPluginInstallerTest extends TestCase
{
    protected $io;
    protected $composer;
    protected $config;
    protected $test;

    public function setUp()
    {
        $this->io = Mockery::mock(IOInterface::class);
        $this->composer = Mockery::mock(Composer::class);
        $this->composer->allows([
            'getPackage' => $this->composer,
            'getDownloadManager' => $this->composer,
            'getConfig' => $this->composer,
            'get' => $this->composer,
        ])->shouldReceive('getExtra')->byDefault();

        $this->test = new LaravelModuleInstaller(
            $this->io, $this->composer
        );
    }

    /**
     * @test
     *
     * Your package composer.json file must include:
     *
     *    "type": "laravel-plugin",
     */
    public function it_supports_laravel_module_type_only()
    {
        $this->assertFalse($this->test->supports('plugin'));
        $this->assertTrue($this->test->supports('laravel-plugin'));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_if_given_malformed_name()
    {
        $mock = $this->getMockPackage('vendor');

        $this->test->getInstallPath($mock);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_if_suffix_not_included()
    {
        $mock = $this->getMockPackage('vendor/name');

        $this->test->getInstallPath($mock);
    }

    /**
     * @test
     */
    public function it_returns_modules_folder_by_default()
    {
        $mock = $this->getMockPackage('vendor/name-plugin');

        $this->assertEquals('Modules/Name', $this->test->getInstallPath($mock));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_if_given_malformed_compound_name()
    {
        $mock = $this->getMockPackage('vendor/some-compound-name');

        $this->assertEquals('Modules/Name', $this->test->getInstallPath($mock));
    }

    /**
     * @test
     */
    public function it_can_use_compound_module_names()
    {
        $mock = $this->getMockPackage('vendor/compound-name-plugin');

        $this->assertEquals('Modules/CompoundName', $this->test->getInstallPath($mock));
    }

    /**
     * @test
     *
     * You can optionally include a base path name
     * in which to install.
     *
     *    "extra": {
     *      "plugin-dir": "Custom"
     *    },
     */
    public function it_can_use_custom_path()
    {
        $package = $this->getMockPackage('vendor/name-plugin');

        $this->composer->shouldReceive('getExtra')
            ->andReturn(['plugin-dir' => 'Custom'])
            ->getMock();

        $this->assertEquals('Custom/Name', $this->test->getInstallPath($package));
    }


    private function getMockPackage($return)
    {
        return Mockery::mock(PackageInterface::class)
            ->shouldReceive('getPrettyName')
            ->once()
            ->andReturn($return)
            ->getMock();
    }

}
