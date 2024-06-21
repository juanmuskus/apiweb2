<?php
require_once 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware{
    private $secretKey;
    public function __construct($secretKey){
        $this->secretKey = $secretKey;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response{
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            throw new HttpUnauthorizedException($request, "No hay cabecera de autorización");
        }
        $token = str_replace('Bearer ', '', $authHeader);//bearer = portador
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $request = $request->withAttribute('jwt', $decoded);
        } catch (Exception $e) {
            throw new HttpUnauthorizedException($request, "Token invalido: " . $e->getMessage());
        }
        return $handler->handle($request);
    }
}
?>
