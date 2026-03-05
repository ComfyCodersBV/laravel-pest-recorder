<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Environment;

use Symfony\Component\Process\Process;

class ScreenResolutionDetector
{
    public function detect(): ?string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin'  => $this->detectMac(),
            'Linux'   => $this->detectLinux(),
            'Windows' => $this->detectWindows(),
            default   => null,
        };
    }

    private function detectMac(): ?string
    {
        $process = new Process(['osascript', '-e', 'tell application "Finder" to get bounds of window of desktop']);
        $process->run();

        if ($process->isSuccessful() && preg_match('/(\d+),\s*(\d+),\s*(\d+),\s*(\d+)/', trim($process->getOutput()), $m)) {
            return ($m[3] - $m[1]) . ',' . ($m[4] - $m[2]);
        }

        return null;
    }

    private function detectLinux(): ?string
    {
        $process = new Process(['wmctrl', '-d']);
        $process->run();

        if ($process->isSuccessful() && preg_match('/WA:\s*\d+,\d+\s+(\d+)x(\d+)/', $process->getOutput(), $m)) {
            return $m[1] . ',' . $m[2];
        }

        $process = new Process(['xdpyinfo']);
        $process->run();

        if ($process->isSuccessful() && preg_match('/dimensions:\s*(\d+)x(\d+)/', $process->getOutput(), $m)) {
            return $m[1] . ',' . $m[2];
        }

        $process = new Process(['xrandr', '--current']);
        $process->run();

        if ($process->isSuccessful() && preg_match('/(\d+)x(\d+)\s+\d+\.\d+\*/', $process->getOutput(), $m)) {
            return $m[1] . ',' . $m[2];
        }

        return null;
    }

    private function detectWindows(): ?string
    {
        $process = new Process([
            'powershell', '-NoProfile', '-NonInteractive', '-Command',
            'Add-Type -AssemblyName System.Windows.Forms; ' .
            '$s = [System.Windows.Forms.Screen]::PrimaryScreen.WorkingArea; ' .
            'Write-Output ($s.Width.ToString() + "," + $s.Height.ToString())',
        ]);
        $process->run();

        $output = trim($process->getOutput());

        if ($process->isSuccessful() && preg_match('/^\d+,\d+$/', $output)) {
            return $output;
        }

        return null;
    }
}
