<?php


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */
class TestHelper
{
	private static $aimeos;
	private static $context = [];


	public static function bootstrap()
	{
		$aimeos = self::getAimeos();

		$includepaths = $aimeos->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	public static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once 'Bootstrap.php';
			spl_autoload_register( 'Aimeos\\Bootstrap::autoload' );

			self::$aimeos = new \Aimeos\Bootstrap();
		}

		return self::$aimeos;
	}


	public static function context( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	private static function createContext( $site )
	{
		$ctx = new \Aimeos\MShop\Context();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths();
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';

		$conf = new \Aimeos\Base\Config\PHPArray( array(), $paths );
		$ctx->setConfig( $conf );


		$dbm = new \Aimeos\Base\DB\Manager\Standard( $conf->get( 'resource', [] ), 'DBAL' );
		$ctx->setDatabaseManager( $dbm );


		$logger = new \Aimeos\Base\Logger\File( $site . '.log', \Aimeos\Base\Logger\Iface::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new \Aimeos\Base\Cache\None();
		$ctx->setCache( $cache );


		$passwd = new \Aimeos\Base\Password\Standard();
		$ctx->setPassword( $passwd );


		$mail = new \Aimeos\Base\Mail\Manager\None();
		$ctx->setMail( $mail );


		$i18n = new \Aimeos\Base\Translation\None( 'en' );
		$ctx->setI18n( array( 'en' => $i18n ) );


		$session = new \Aimeos\Base\Session\None();
		$ctx->setSession( $session );


		$localeManager = \Aimeos\MShop::create( $ctx, 'locale' );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );

		$view = self::view( $site, $conf );
		$ctx->setView( $view );


		return $ctx->setEditor( 'ai-cms-grapesjs' );
	}


	public static function view( $site = 'unittest', ?\Aimeos\Base\Config\Iface $config = null )
	{
		$aimeos = self::getAimeos();

		if( $config === null ) {
			$config = self::context( $site )->config();
		}

		$templates = array_merge_recursive(
			$aimeos->getTemplatePaths( 'admin/jqadm/templates' ),
			$aimeos->getTemplatePaths( 'client/html/templates' ),
			$aimeos->getTemplatePaths( 'client/jsonapi/templates' )
		);

		$view = new \Aimeos\Base\View\Standard( $templates );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, ['site' => 'unittest'] );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\Base\View\Helper\Access\All( $view );
		$view->addHelper( 'access', $helper );

		$trans = new \Aimeos\Base\Translation\None( 'de_DE' );
		$helper = new \Aimeos\Base\View\Helper\Translate\Standard( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\Base\View\Helper\Url\Standard( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\Base\View\Helper\Number\Standard( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\Base\View\Helper\Date\Standard( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$config = new \Aimeos\Base\Config\Decorator\Protect( $config, ['version', 'admin', 'client', 'resource/fs/baseurl', 'resource/fs-media/baseurl', 'resource/fs-theme/baseurl'] );
		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\Base\View\Helper\Session\Standard( $view, new \Aimeos\Base\Session\None() );
		$view->addHelper( 'session', $helper );

		$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
		$helper = new \Aimeos\Base\View\Helper\Request\Standard( $view, $psr17Factory->createServerRequest( 'GET', 'https://aimeos.org' ) );
		$view->addHelper( 'request', $helper );

		$helper = new \Aimeos\Base\View\Helper\Response\Standard( $view, $psr17Factory->createResponse() );
		$view->addHelper( 'response', $helper );

		$helper = new \Aimeos\Base\View\Helper\Csrf\Standard( $view, '_csrf_token', '_csrf_value' );
		$view->addHelper( 'csrf', $helper );

		$view->pageSitePath = [];

		return $view;
	}
}