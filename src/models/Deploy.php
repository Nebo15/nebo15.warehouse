<?php
namespace Models;

use Guzzle\Http\Client;

class Deploy {
    public function run($data, $ips)
    {
        if ($data && $ips and is_array($ips)) {
            foreach ($ips as $ip) {
                $client = new Client('http://' . $ip);
                $client->setDefaultOption('headers', ['Content-type' => 'application/json']);
                $client->setDefaultOption('exceptions', false);
                $client->setDefaultOption('timeout', 20);
                $client->setDefaultOption('connecttimeout', 0);
                $client->setDefaultOption('debug', false);
                $request = $client->post("/", [], []);
                $request->setBody($data);
                $request->send();
            }
        }
    }
}