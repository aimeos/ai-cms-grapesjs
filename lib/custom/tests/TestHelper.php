<?php


class TestHelper
{
	private static $aimeos;
	private static $context = array();


	public static function bootstrap()
	{
		$aimeos = self::getAimeos();

		$includepaths = $aimeos->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	private static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once 'Bootstrap.php';
			spl_autoload_register( 'Aimeos\\Bootstrap::autoload' );

			self::$aimeos = new \Aimeos\Bootstrap();
		}

		return self::$aimeos;
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	public static function getHtmlTemplatePaths()
	{
		return self::getAimeos()->getTemplatePaths( 'client/html/templates' );
	}


	/**
	 * @param string $site
	 */
	private static function createContext( $site )
	{
		$ctx = new \Aimeos\MShop\Context\Item\Standard();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths();
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';

		$conf = new \Aimeos\MW\Config\PHPArray( array(), $paths );
		$ctx->setConfig( $conf );


		$dbm = new \Aimeos\MW\DB\Manager\DBAL( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new \Aimeos\MW\Logger\File( $site . '.log', \Aimeos\MW\Logger\Base::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new \Aimeos\MW\Cache\None();
		$ctx->setCache( $cache );


		$i18n = new \Aimeos\MW\Translation\None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$session = new \Aimeos\MW\Session\None();
		$ctx->setSession( $session );


		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $ctx );
		$localeItem = $localeManager->bootstrap( $site, '', '', false );

		$ctx->setLocale( $localeItem );

		$ctx->setEditor( 'ai-cms-grapesjs:lib/custom' );

		return $ctx;
	}


	public static function view( $site = 'unittest', \Aimeos\MW\Config\Iface $config = null )
	{
		if( $config === null ) {
			$config = self::getContext( $site )->getConfig();
		}

		$view = new \Aimeos\MW\View\Standard( self::getHtmlTemplatePaths() );

		$trans = new \Aimeos\MW\Translation\None( 'de_DE' );
		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\MW\View\Helper\Url\Standard( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\MW\View\Helper\Number\Standard( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\Date\Standard( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$config = new \Aimeos\MW\Config\Decorator\Protect( $config, ['client', 'resource/fs/baseurl'] );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, '_csrf_token', '_csrf_value' );
		$view->addHelper( 'csrf', $helper );

		$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $view, $psr17Factory->createServerRequest( 'GET', 'https://aimeos.org' ) );
		$view->addHelper( 'request', $helper );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $view, $psr17Factory->createResponse() );
		$view->addHelper( 'response', $helper );

		return $view;
	}
}
