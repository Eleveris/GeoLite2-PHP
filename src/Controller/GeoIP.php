<?php

namespace geolite2php\Controller;

use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Exception\AuthenticationException;
use geolite2php\Model\GeoIPLocal;
use geolite2php\Model\GeoIPRemote;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class GeoIP
{
    public function getRemote(Request $request, Response $response, array $args): Response
    {
        try {
            $model = new GeoIPRemote();
            $ip = $this->getIP($request->getQueryParams());
            $result = $model->get($ip);
        } catch (InvalidDatabaseException) {
            return $this->errorResponse($response, 'База данных недоступна', 500);
        } catch (AuthenticationException) {
            return $this->errorResponse($response, 'Ошибка авторизации в сервисе GeoIP2', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse($response, $e->getMessage(), 500);
        }
        return $this->successResponse($response, $result);
    }

    public function getLocal(Request $request, Response $response, array $args): Response
    {
        try {
            $ip = $this->getIP($request->getQueryParams());
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode());
        }
        try {
            $model = new GeoIPLocal();
            $result = $model->get($ip);
        } catch (InvalidDatabaseException) {
            return $this->errorResponse($response, 'База данных недоступна', 500);
        } catch (AddressNotFoundException) {
            return $this->errorResponse($response, 'Адрес не был найден в базе данных', 404);
        } catch (\Throwable $e) {
            return $this->errorResponse($response, $e->getMessage(), 500);
        }
        return $this->successResponse($response, $result);
    }

    protected function getIP(array $body): string
    {
        if (!isset($body['ip'])) {
            throw new \Exception("В запросе отсутствует параметр ip", 400);
        }
        if (!filter_var($body['ip'], FILTER_VALIDATE_IP)) {
            throw new \Exception("Параметр ip не является валидным ip адресом", 400);
        }
        return $body['ip'];
    }

    protected function errorResponse(Response $response, string $error, int $code): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->withStatus($code);
        $response->getBody()->write(json_encode(['error' => $error, 'errcode' => $code], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $response;
    }

    protected function successResponse(Response $response, array|string $value): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['result' => $value], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $response;
    }
}