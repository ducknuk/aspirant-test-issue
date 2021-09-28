<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class HomeController
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     *
     * @throws HttpBadRequestException
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('home/index.html.twig', [
                'trailers' => $this->em->getRepository(Movie::class)->findAll(),
                'data' => date('Y-m-d'),
                'day' => date('l'),
                'time' => date('h:i'),
                'controller_name' => (new \ReflectionClass($this))->getShortName(),
                'method_name' => __FUNCTION__,
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     *
     * @throws HttpBadRequestException
     */
    public function getTrailer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $trailer = $this->em->getRepository(Movie::class)->find($args['id']);
            $data = $this->twig->render('home/trailer.html.twig', [
                'trailer' => $trailer,
                'home' => $this->routeCollector->getNamedRoute('main')->getPattern(),
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }
}
