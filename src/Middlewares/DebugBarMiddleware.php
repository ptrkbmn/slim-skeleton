<?php

declare(strict_types=1);

namespace App\Middlewares;

use DebugBar\DebugBar as Bar;
use DebugBar\StandardDebugBar;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

class DebugBarMiddleware implements MiddlewareInterface
{
    private static $mimes = [
        'css' => 'text/css',
        'js' => 'text/javascript',
    ];

    /**
     * @var Bar|null The debugbar
     */
    private $debugbar;

    /**
     * @var bool Whether send data using headers in ajax requests
     */
    private $captureAjax = false;

    /**
     * @var bool Whether dump the css/js code inline in the html
     */
    private $inline = false;

    /**
     * @var bool Whether dump the css/js code in the public folder
     */
    private $cssJsInPublicFolder = false;
    private $publicFolderPath = null;

    /**
     * Set the debug bar.
     */
    public function __construct(Bar $debugbar = null)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * Configure whether capture ajax requests to send the data with headers.
     */
    public function captureAjax(bool $captureAjax = true): self
    {
        $this->captureAjax = $captureAjax;
        return $this;
    }

    /**
     * Configure whether the js/css code should be inserted inline in the html.
     */
    public function inline(bool $inline = true): self
    {
        $this->inline = $inline;
        return $this;
    }

    /**
     * Configure whether the js/css code should be inserted inline in the html.
     */
    public function cssJsInPublicFolder(bool $cssJsInPublicFolder = true, string $path = null): self
    {
        $this->cssJsInPublicFolder = $cssJsInPublicFolder;
        $this->publicFolderPath = $path;
        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $debugbar = $this->debugbar ?: new StandardDebugBar();
        $renderer = $debugbar->getJavascriptRenderer();

        //Asset response
        $path = $request->getUri()->getPath();
        $baseUrl = $renderer->getBaseUrl();

        if (strpos($path, $baseUrl) === 0) {
            $file = $renderer->getBasePath() . substr($path, strlen($baseUrl));

            if (file_exists($file)) {
                $responseFactory = new ResponseFactory();
                $response = $responseFactory->createResponse();
                $response->getBody()->write(file_get_contents($file));
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                if (isset(self::$mimes[$extension])) {
                    return $response->withHeader('Content-Type', self::$mimes[$extension]);
                }

                return $response; //@codeCoverageIgnore
            }
        }

        $response = $handler->handle($request);

        $isAjax = strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';

        //Redirection response
        if (in_array($response->getStatusCode(), [302, 301])) {
            return $this->handleRedirect($debugbar, $response);
        }

        //Html response
        $contentType = $response->getHeaderLine('Content-Type');
        if (empty($contentType)) {
            $contentType = "text/html";
        }

        if (stripos($contentType, 'text/html') === 0) {
            return $this->handleHtml($debugbar, $response, $isAjax);
        }

        //Ajax response
        if ($isAjax && $this->captureAjax) {
            $headers = $debugbar->getDataAsHeaders();

            foreach ($headers as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }

    /**
     * Handle redirection responses
     */
    private function handleRedirect(Bar $debugbar, ResponseInterface $response): ResponseInterface
    {
        if ($debugbar->isDataPersisted() || session_status() === PHP_SESSION_ACTIVE) {
            $debugbar->stackData();
        }

        return $response;
    }

    /**
     * Handle html responses
     */
    private function handleHtml(Bar $debugbar, ResponseInterface $response, bool $isAjax): ResponseInterface
    {
        $html = (string) $response->getBody();
        $renderer = $debugbar->getJavascriptRenderer();

        $scriptBody = "";
        if (!$isAjax) {
            if ($this->cssJsInPublicFolder && $this->publicFolderPath) {
                $debugbarCss = $this->publicFolderPath . DIRECTORY_SEPARATOR . "debugbar.css";
                if (!file_exists($debugbarCss)) {
                    ob_start();
                    $renderer->dumpCssAssets();
                    $css = ob_get_clean();
                    file_put_contents($debugbarCss, $css);
                }
                $code = '<link rel="stylesheet" href="/debugbar.css">';

                $debugbarJs = $this->publicFolderPath . DIRECTORY_SEPARATOR . "debugbar.js";
                if (!file_exists($debugbarJs)) {
                    ob_start();
                    $renderer->dumpJsAssets();
                    $js = ob_get_clean();
                    file_put_contents($debugbarJs, $js);
                }
                $scriptBody = '<script src="/debugbar.js"></script>';
            } else if ($this->inline) {
                ob_start();
                echo "<style>\n";
                $renderer->dumpCssAssets();
                echo "\n</style>";
                $code = ob_get_clean();

                ob_start();
                echo "<script>\n";
                $renderer->dumpJsAssets();
                echo "\n</script>\n";
                $scriptBody = ob_get_clean();
            } else {
                $code = $renderer->renderHead();
            }

            $html = self::injectHtml($html, $code, '</head>');
        }


        $html = self::injectHtml($html, $scriptBody . $renderer->render(!$isAjax), '</body>');

        $factory = new StreamFactory();
        $body = $factory->createStream();
        $body->write($html);

        return $response
            ->withBody($body)
            ->withoutHeader('Content-Length');
    }

    /**
     * Inject html code before a tag.
     */
    private static function injectHtml(string $html, string $code, string $before): string
    {
        $pos = strripos($html, $before);

        if ($pos === false) {
            return $html . $code;
        }

        return substr($html, 0, $pos) . $code . substr($html, $pos);
    }
}
