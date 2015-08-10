<?php
$app->get('/:project/:environment', function($project, $environment) use ($app) {
    $file = dirname(__FILE__) . '/../../settings/servers/' . $project . '_' . $environment;
    $content = file_exists($file) ? file($file) : [];
    foreach ($content as &$line) {
        $line = trim($line);
    }
    $app->response->setStatus(200);
    echo json_encode($content);
});

$app->post('/', function() use ($app) {
    $request = json_decode($app->request->getBody());
    if (!$request->project || !$request->env || !$request->ip) {
        $app->response->setStatus(500);
        return $app->response->finalize();
    }
    $file = dirname(__FILE__) . '/../../settings/servers/' . $request->project . '_' . $request->env;
    $content = file_exists($file) ? file($file) : [];
    foreach ($content as &$ip) {
        $ip = trim($ip);
    }
    $content[] = $request->ip;
    if (file_put_contents($file, join("\n",$content))) {
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }
});

$app->delete('/', function() use ($app) {
    $request = json_decode($app->request->getBody());
    if (!$request->project || !$request->env || !$request->ip) {
        $app->response->setStatus(500);
        return $app->response->finalize();
    }
    $file = dirname(__FILE__) . '/../../settings/servers/' . $request->project . '_' . $request->env;
    $content = file_exists($file) ? file($file) : [];
    $new_content = [];
    foreach ($content as $key=>$ip) {
        $ip = trim($ip);
        if ($ip != $request->ip) {
            $new_content[] = $ip;
        }
    }
    $content = join("\n", $new_content);
    if (file_put_contents($file, $content)) {
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }
});
