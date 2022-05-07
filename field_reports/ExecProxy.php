<?php
declare(strict_types=1);
namespace FieldReports;

use Exception;
use RuntimeException;
use FieldReports\ReportsException;

/// @cond
class ExecProxy implements Proxy
{
    private $exe_path;
    private $cwd;
    private $loglevel;
    private $stderr;
    private $descriptor;

    public function __construct(string $exe_path, string $cwd, int $loglevel, $logout)
    {
        $this->stderr = fopen('php://stderr', 'w');
        $this->exe_path = $exe_path;
        $this->cwd = $cwd;
        $this->loglevel = $loglevel;
        $this->descriptor = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => ($logout == null) ? $this->stderr : $logout
        );
    }

    function __destruct()
    {
        fclose($this->stderr);
    }

    private function command(string ...$args)
    {
        array_unshift($args, $this->exe_path);
        return join(' ', $args);
    }

    public function version(): string
    {
        try {
            $process = proc_open($this->command('version'),
                $this->descriptor, $pipes, $this->cwd, null, ["bypass_shell" => true]);
            if (is_resource($process)) {
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $return_value = proc_close($process);
                if ($return_value != 0) {
                    throw new RuntimeException(sprintf("Exit Code = %d", $return_value));
                }
                return $output;
            } else {
                throw new RuntimeException(sprintf("exe_path = %s", $this->exe_path));
            }
        } catch (Exception $exn) {
            throw new ReportsException($exn, "Process terminated abnormally");
        }
    }

    public function render(mixed $param): string
    {
        try {
            $jparam = is_array($param) ? json_encode($param, JSON_UNESCAPED_UNICODE) : $param;
            $process = proc_open($this->command('render', '-', '-'),
                $this->descriptor, $pipes, $this->cwd, null, ["bypass_shell" => true]);
            if (is_resource($process)) {
                fwrite($pipes[0], $jparam);
                fclose($pipes[0]);
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $return_value = proc_close($process);
                if ($return_value != 0) {
                    throw new RuntimeException(sprintf("Exit Code = %d", $return_value));
                }
                return $output;
            } else {
                throw new RuntimeException(sprintf("exe_path = %s", $this->exe_path));
            }
        } catch (Exception $exn) {
            throw new ReportsException($exn, "Process terminated abnormally");
        }
    }

    public function parse(string $pdf): array
    {
        try {
            $process = proc_open($this->command('parse', '-'),
                $this->descriptor, $pipes, $this->cwd, null, ["bypass_shell" => true]);
            if (is_resource($process)) {
                fwrite($pipes[0], $pdf);
                fclose($pipes[0]);
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $return_value = proc_close($process);
                if ($return_value != 0) {
                    throw new RuntimeException(sprintf("Exit Code = %d", $return_value));
                }
                return json_decode($output, true);
            } else {
                throw new RuntimeException(sprintf("exe_path = %s", $this->exe_path));
            }
        } catch (Exception $exn) {
            throw new ReportsException($exn, "Process terminated abnormally");
        }
    }
}
/// @endcond