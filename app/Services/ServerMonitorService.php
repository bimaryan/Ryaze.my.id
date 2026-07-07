<?php

namespace App\Services;

class ServerMonitorService
{
    /**
     * Get overall server status (CPU, RAM, Disk)
     */
    public static function getStatus(): array
    {
        $cpu = self::getCpuLoad();
        $ram = self::getRamUsage();
        $disk = self::getDiskUsage();
        
        $uptime = shell_exec('uptime -p');
        if ($uptime) {
            $uptime = trim(str_replace('up ', '', $uptime));
        } else {
            $uptime = 'Unknown';
        }

        return [
            'cpu' => $cpu,
            'ram' => $ram,
            'disk' => $disk,
            'uptime' => $uptime
        ];
    }

    private static function getCpuLoad(): array
    {
        $load = sys_getloadavg();
        // Return 1 minute average load as a percentage (approximate based on cores if known, assuming 100% is full load, but load is just process queue)
        // Better way to get CPU usage percentage on Linux:
        $cpuUsage = 0;
        if (is_readable('/proc/stat')) {
            $stat1 = file('/proc/stat');
            sleep(1);
            $stat2 = file('/proc/stat');
            $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0]));
            $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0]));
            $dif['user'] = $info2[0] - $info1[0];
            $dif['nice'] = $info2[1] - $info1[1];
            $dif['sys'] = $info2[2] - $info1[2];
            $dif['idle'] = $info2[3] - $info1[3];
            $total = array_sum($dif);
            if ($total > 0) {
                $cpuUsage = round(100 * ($total - $dif['idle']) / $total, 1);
            }
        }
        
        return [
            'load_1m' => $load[0] ?? 0,
            'load_5m' => $load[1] ?? 0,
            'load_15m' => $load[2] ?? 0,
            'usage_percent' => $cpuUsage
        ];
    }

    private static function getRamUsage(): array
    {
        $free = shell_exec('free -m');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        
        if (count($free_arr) >= 2) {
            $mem = explode(" ", preg_replace("!\s+!", " ", $free_arr[1])); // Mem: total used free shared buff/cache available
            $total = (float)$mem[1];
            $used = (float)$mem[2];
            
            if ($total > 0) {
                $percentage = round(($used / $total) * 100, 1);
                return [
                    'total_mb' => $total,
                    'used_mb' => $used,
                    'percentage' => $percentage
                ];
            }
        }
        
        return [
            'total_mb' => 0,
            'used_mb' => 0,
            'percentage' => 0
        ];
    }

    private static function getDiskUsage(): array
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        
        if ($total > 0) {
            return [
                'total_gb' => round($total / 1024 / 1024 / 1024, 1),
                'used_gb' => round($used / 1024 / 1024 / 1024, 1),
                'free_gb' => round($free / 1024 / 1024 / 1024, 1),
                'percentage' => round(($used / $total) * 100, 1)
            ];
        }
        
        return [
            'total_gb' => 0,
            'used_gb' => 0,
            'free_gb' => 0,
            'percentage' => 0
        ];
    }
}
