<?php


namespace App\Http\Controllers\Installer;

use Illuminate\Routing\Controller;
use App\Events\Installer\LaravelInstallerFinished;
use App\Helper\Installer\EnvironmentManager;
use App\Helper\Installer\FinalInstallManager;
use App\Helper\Installer\InstalledFileManager;

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     *
     * @param  \RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager  $fileManager
     * @param  \RachidLaasri\LaravelInstaller\Helpers\FinalInstallManager  $finalInstall
     * @param  \RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager  $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        event(new LaravelInstallerFinished);

        return view('installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}
