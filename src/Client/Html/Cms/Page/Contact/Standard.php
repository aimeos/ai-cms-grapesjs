<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Cms\Page\Contact;


/**
 * Default implementation for CMS contact form.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		return '';
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modify( string $content, string $uid ) : string
	{
		if( !$this->view()->access( ['editor', 'admin', 'super'] ) )
		{
			$csrf = $this->view()->csrf();
			$content = str_replace( ['%csrf.name%', '%csrf.value%'], [$csrf->name(), $csrf->value()], $content );
		}

		return $content;
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();

		$name = $view->param( 'contact/name' );
		$email = $view->param( 'contact/email' );
		$msg = $view->param( 'contact/message' );
		$honeypot = $view->param( 'contact/url' );

		if( !$honeypot && $email && $msg )
		{
			$context = $this->context();
			$config = $context->config();

			$toAddr = $config->get( 'resource/email/from-email' );
			$toName = $config->get( 'resource/email/from-name' );

			if( $toAddr )
			{
				$label = $context->locale()->getSiteItem()->getLabel();

				$context->mail()->create()
					->to( $toAddr, $toName )
					->from( $email, $name )
					->subject( $context->translate( 'client', 'Your request' ) . ' - ' . $label )
					->text( $msg )
					->send();

				$info = [$context->translate( 'client', 'Message sent successfully' )];
				$view->infos = array_merge( $view->get( 'infos', [] ), $info );
			}
			else
			{
				$error = [$context->translate( 'client', 'No recipient configured' )];
				$view->errors = array_merge( $view->get( 'errors', [] ), $error );
			}
		}
	}
}
