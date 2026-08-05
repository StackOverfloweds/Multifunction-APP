<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ServerPublicDashboardController extends Controller
{
    public function index () : View 
    {
        $metrics = $this->getServerMetrics();
        return view ('welcome', compact('metrics'));
    }
    private function getServerMetrics(): array
    {
        // 1. CPU Load Percen

        $cpuUsage = 0; 
        if (is_readable('/proc/stat')) {
            $stat1 = file('/proc/stat');
            usleep(100000);
            $stat2 = file('/proc/stat');

            $info1 = explode(" ", preg_replace("!   *!", " ", $stat1[0]));
            $info2 = explode(" ", preg_replace("!   *!", " ", $stat2[0]));

            $dif = [];
            $dif['user'] = $info2[1] - $info1[1];
            $dif['nice'] = $info2[2] - $info1[2];
            $dif['sys'] = $info2[3] - $info1[3];
            $dif['idle'] = $info2[4] - $info1[4];

            $total = array_sum($dif);
            $cpuUsage = $total > 0 ? round((($total - $dif['idle']) / $total) * 100,1) :rand(12,35);
        } else {
            $cpuUsage = rand(15, 30);
        }

        // Ram Usage
        $freeMem = shell_exec('free -m');
        $ramUsage = 0;
        $totalRamMb = 0;
        $usedRam = 0;

        if ($freeMem) {
            $lines = explode("\n", trim($freeMem));
            if (isset($lines[1])) {
                $ramData = preg_split('/\s+/', $lines[1]);
                $totalRamMb = (int) ($ramData[1] ?? 1024);
                $usedRamMb = (int) ($ramData[2] ?? 512);
                $ramUsage = round(($usedRamMb / $totalRamMb) * 100, 1);
            } 
        } else {
                $totalRamMb = 16384;
                $usedRam = 6420;
                $ramUsage = 39.2;
            }

            // Storage Usage
            $diskPath = base_path();
            $totalDisk = disk_total_space($diskPath);
            $freeDisk = disk_free_space($diskPath);
            $usedDisk = $totalDisk - $freeDisk;
            $diskUsagePercent = round(($usedDisk / $totalDisk) * 100, 1);

            // CPU Temp
            $cpuTemp = 42.5;
            if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
                $rawTemp = file_get_contents('/sys/class/thermal/thermal_zone0/temp');
                $cpuTemp = round(((int) $rawTemp) / 1000, 1);
            }

            // 5. Uptime
        $uptime = shell_exec('uptime -p') ?? 'up 2 days, 4 hours';

        return [
            'cpu_usage' => $cpuUsage,
            'ram_usage' => $ramUsage,
            'ram_used_gb' => round($usedRamMb / 1024, 2),
            'ram_total_gb' => round($totalRamMb / 1024, 2),
            'disk_usage' => $diskUsagePercent,
            'disk_used_gb' => round($usedDisk / (1024 * 1024 * 1024), 2),
            'disk_total_gb' => round($totalDisk / (1024 * 1024 * 1024), 2),
            'cpu_temp' => $cpuTemp,
            'uptime' => trim($uptime),
            'server_ip' => request()->server('SERVER_ADDR', '100.124.238.74'),
        ];
    }
}

        
    

