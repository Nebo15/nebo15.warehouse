<?php
$app->group('/ips', function () use ($app) {
    $app->get('/:project/:environment', function ($project, $environment) use ($app) {
        $app->modelIps->setProject($project);
        $app->modelIps->setEnv($environment);
        $app->response->setStatus(200);
        echo json_encode($app->modelIps->getIps());
    });

    $app->post('/', function () use ($app) {
        $request = json_decode($app->request->getBody());
        if (!$request->project || !$request->env || !$request->ip) {
            $app->response->setStatus(500);
            return $app->response->finalize();
        }
        $app->modelIps->setProject($request->project);
        $app->modelIps->setEnv($request->env);
        $answer = json_encode($app->modelIps->addIp($request->ip));
        echo $answer;
        return $answer;
    });

    $app->delete('/', function () use ($app) {
        $request = json_decode($app->request->getBody());
        if (!$request->project || !$request->env || !$request->ip) {
            $app->response->setStatus(500);
            return $app->response->finalize();
        }
        $app->modelIps->setProject($request->project);
        $app->modelIps->setEnv($request->env);
        $answer = json_encode($app->modelIps->removeIp($request->ip));
        echo $answer;
        return $answer;
    });
});

$app->group('/autodeploy', function() use($app) {
    $app->post('/', function () use ($app) {
        $payload = $app->request->params();

        $app->logger->log($payload);
        exit;
        if (is_string($payload)) {
            $data = json_decode($payload);
            if ($data->state == 'passed') {
                $commit_id = $data->commit;
                $app->modelIps->setProject($data->repository->name);
                $app->modelIps->setEnv($data->branch);
                $app->modelIps->setProject('mbank.api');
                $app->modelIps->setEnv('develop');
                $body = json_encode(['project'=>$data->repository->name, 'commit' => $commit_id]);
                $ips = $app->modelIps->getIps();
                foreach ($ips as $ip) {
                    $client = new \Guzzle\Http\Client('http://'.$ip);
                    $client->setDefaultOption('headers', ['Content-type' => 'application/json']);
                    $client->setDefaultOption('exceptions', false);
                    $client->setDefaultOption('timeout', 20);
                    $client->setDefaultOption('connecttimeout', 0);
                    $client->setDefaultOption('debug', false);
                    $request = $client->post("/", [], []);
                    $request->setBody($body);
                    $request->send();
                }

            }
        }
    });
});