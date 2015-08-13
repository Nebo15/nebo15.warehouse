<?php
namespace Models;

class Ips {

    private $project;
    private $env;

    public function setProject($project)
    {
        $this->project = $project;
    }

    public function setEnv($env)
    {
        $this->env = $env;
    }

    public function addIp($new_ip)
    {
        $file = $this->getFileName();
        $content = file_exists($file) ? file($file) : [];
        foreach ($content as &$ip) {
            $ip = trim($ip);
        }
        $content[] = $new_ip;
        return file_put_contents($file, join("\n", $content)) ? true : false;
    }

    public function removeIp($remove_ip)
    {
        $file = $this->getFileName();
        $content = file_exists($file) ? file($file) : [];
        $new_content = [];
        foreach ($content as $key => $ip) {
            $ip = trim($ip);
            if ($ip != $remove_ip) {
                $new_content[] = $ip;
            }
        }
        $content = join("\n", $new_content);
        return file_put_contents($file, $content) ? true : false;
    }

    public function getIps()
    {
        $file = $this->getFileName();
        $content = file_exists($file) ? file($file) : [];
        foreach ($content as &$line) {
            $line = trim($line);
        }
        return $content;
    }

    private function getFileName()
    {
        return dirname(__FILE__) . '/../../settings/servers/' . $this->project . '_' . $this->env;
    }
}