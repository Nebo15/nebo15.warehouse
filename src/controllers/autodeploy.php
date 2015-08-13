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
    $app->get('/', function () use ($app) {
        $request = $app->request->getBody();
        $app->logger->log($request);
    });
    $app->post('/', function () use ($app) {
        $request = $app->request->getBody();
        $app->logger->log($request);
    });
});