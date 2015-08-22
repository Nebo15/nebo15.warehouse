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
                $repo_name = $data->pull_request->base->repo->name;
                $branch = $data->pull_request->base->ref;
            } elseif (property_exists($data, 'head_commit') && property_exists($data, 'ref')) {
                $repo_name = $data->repository->name;
                list(,,$branch) = explode('/', $data->ref);
            }
            if ($branch and $repo_name) {
                $app->modelIps->setProject($repo_name);
                $app->modelIps->setEnv($branch);
                $body = json_encode(['project'=>$repo_name, 'branch' => $branch]);
                $app->deploy->run($body, $app->modelIps->getIps());
            }
        }
    });
});