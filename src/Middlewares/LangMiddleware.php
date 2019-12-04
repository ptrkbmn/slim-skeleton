<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Helpers\DebugbarWrapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class LangMiddleware implements Middleware
{
  private $locale_directory;
  private $locale_domain;
  private $languages;

  public function __construct()
  {
    $this->locale_directory = ROOT_PATH . "/locale";
    $this->locale_domain = 'messages';
    $this->languages = [
      'en' => ['code' => 'en', 'locale' => 'en_GB', 'label' => 'English', 'flag' => 'gb'],
      'de' => ['code' => 'de', 'locale' => 'de_DE', 'label' => 'Deutsch (German)', 'flag' => 'de'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function process(Request $request, RequestHandler $handler): Response
  {
    $session = $request->getAttribute('session');
    if (!isset($session['lang'])) {
      $session['lang'] = $this->get_browser_language(array_keys($this->languages));
    }

    $lang = $session['lang'];
    $locale = $this->languages[$lang]['locale'];
    putenv('LANG=' . $locale);
    setlocale(LC_ALL, $locale);

    // Workaround to avoid gettext caching problem
    // - Copy the locale file and append the modification date
    $locale_filename = $this->locale_directory . "/" . $locale . "/LC_MESSAGES/" . $this->locale_domain . ".mo";
    if (file_exists($locale_filename)) {
      $mtime = filemtime($locale_filename);
      $locale_domain_new = $this->locale_domain . "_" . $mtime;
      $locale_filename_new = $this->locale_directory . "/" . $locale . "/LC_MESSAGES/" . $locale_domain_new . ".mo";
      if (!file_exists($locale_filename_new))
        copy($locale_filename, $locale_filename_new);

      // Finally bind the new domain name
      bindtextdomain($locale_domain_new, $this->locale_directory);
      bind_textdomain_codeset($locale_domain_new, 'UTF-8');
      textdomain($locale_domain_new);
    }

    // Apply lang variables to the view
    /** @var \Slim\Views\Twig $view */
    $view = $request->getAttribute('twig');
    $environment = $view->getEnvironment();
    $environment->addGlobal('LANG', $this->languages[$lang]);
    $environment->addGlobal('LANGS', $this->languages);

    return $handler->handle($request);
  }

  /**
   * Get browser language, given an array of avalaible languages.
   */
  private function get_browser_language($available = [], $default = 'en')
  {
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
      if (empty($available)) {
        return $langs[0];
      }
      foreach ($langs as $lang) {
        if (strlen($lang) > 2) {
          $lang = substr($lang, 0, 2);
        }
        if (in_array($lang, $available)) {
          return $lang;
        }
      }
    }
    return $default;
  }
}
