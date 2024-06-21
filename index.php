<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
//jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php';
require 'articulos.php';
require 'JwtMiddleware.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$secretKey = 'unicor@2024';

// Middleware JWT
$jwtMiddleware = new JwtMiddleware($secretKey);


$app->post('/login', function (Request $request, Response $response, $args) use ($secretKey) {
    $body = $request->getParsedBody();
    $username = $body['username'];
    $password = $body['password'];
    // Aquí deberías verificar las credenciales de usuario
    if ($username === 'root' && $password === '123456') {
        $payload = [
            'iss' => 'web2.com', // Emisor del token
            'sub' => $username,     // Sujeto del token (normalmente el ID de usuario)
            'iat' => time(),        // Hora en que fue emitido
            'exp' => time() + 3600  // Tiempo de expiración (1 hora)
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256'); // Codificar el token

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    else{
        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }});

// Ruta protegida
$app->get('/protegido', function (Request $request, Response $response, $args) {
    $jwt = $request->getAttribute('jwt');
    $response->getBody()->write(json_encode(['message' => 'Esta es una ruta protegida', 'Usuario' => $jwt]));
    return $response->withHeader('Content-Type', 'application/json');
})->add($jwtMiddleware);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hola Mundo WEB 2!");
    return $response;
});


$app->get('/articulos', function (Request $request, Response $response, $args) {
    $articulos = new articulos();
    $data = $articulos->getAll();
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/articulos/{id}', function (Request $request, Response $response, $args) {
    $id = $request->getAttribute('id');
    $articulos = new articulos();
    $data = $articulos->getById($id);
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/articulos', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    if (!empty($data)) {
        $articulos = new Articulos();
        $result = $articulos->insertar($data);
        if ($result !== false) {
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Error al insertar el artículo']));
        }
    } else {
        $response->getBody()->write(json_encode(['error' => 'Datos incompletos']));
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/articulos/{id}', function (Request $request, Response $response, $args) {
    $id = $request->getAttribute('id');
    $articulos = new articulos();
    $result = $articulos->delete($id);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/articulos/{id}', function (Request $request, Response $response, $args) {
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
    if (!empty($data)) {
        $articulos = new articulos();
        $result = $articulos->update($id, $data);
        $response->getBody()->write($result);
    } else {
        $response->getBody()->write(json_encode(['error' => 'Datos incompletos']));
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();