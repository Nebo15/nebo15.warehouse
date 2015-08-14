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
        $payload = file_get_contents('php://input');
        $app->logger->log($payload);
        if (is_string($payload)) {
            $data = json_decode($payload);
            if (property_exists($data, 'pull_request') && $data->pull_request->state == 'closed' && $data->pull_request->merged == 1) {
                $repo_name = $data->pull_request->repo->name;
                $branch = $data->pull_request->base->ref;
                $app->modelIps->setProject($repo_name);
                $app->modelIps->setEnv($branch);
                $app->modelIps->setProject('mbank.api');
                $app->modelIps->setEnv('develop');
                $body = json_encode(['project'=>$data->repository->name]);
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