<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Add Slim routing middleware
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);


$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world! world!!2");
    return $response;
});



$app->post('/distance', function (Request $request, Response $response, $args) {
    $request_data  = $request->getParsedBody();
    $biggi_lat  = $request_data['biggi_lat'];
    $biggi_long = $request_data['biggi_long'];
    $other_lat  = $request_data['other_lat'];
    $other_long = $request_data['other_long'];

    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $other_lat . "," . $other_long .
     "&destinations=" . $biggi_lat . "," . $biggi_long . "&mode=driving&language=pl-PL&key=YOUR-KEY";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    // echo var_dump($response_a);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

    $response->getBody()->write(json_encode(array('distance' => $dist, 'time' => $time)));
    return $response;

});

$app->run();
