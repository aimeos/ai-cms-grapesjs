<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Cms\Page\Decorator;


/**
 * Recaptcha decorator for HTML clients
 *
 * @package Client
 * @subpackage Html
 */
class Recaptcha
	extends \Aimeos\Client\Html\Common\Decorator\Base
	implements \Aimeos\Client\Html\Common\Decorator\Iface
{
	/**
	 * Processes the input, e.g. store given values.
	 */
	public function init()
	{
		$view = $this->view();
		$context = $this->context();
		$key = $context->config()->get( 'resource/recaptcha/secretkey' );

		if( $key && $view->request()->getMethod() === 'POST' )
		{
			if( ( $token = $view->param( 'g-recaptcha-response' ) ) === null ) {
				throw new \Aimeos\Client\Html\Exception( $context->translate( 'client', 'reCAPTCHA token missing' ) );
			}

			$ip = $view->request()->getClientAddress();
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $key . '&response=' . $token . '&remoteip=' . $ip;

			if( ( $result = file_get_contents( $url ) ) === false || ( $data = json_decode( $result ) ) === null ) {
				throw new \Aimeos\Client\Html\Exception( $context->translate( 'client', 'Invalid reCAPTCHA response' ) );
			}

			if( $data->success != true || $data->score < 0.5 ) {
				throw new \Aimeos\Client\Html\Exception( $context->translate( 'client', 'Your request is likely spam and was not executed' ) );
			}
		}

		$this->client()->init();
	}


	/**
	 * Returns the HTML string for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML body content
	 */
	public function body( string $uid = '' ) : string
	{
		$context = $this->context();
		$content = $this->client()->body( $uid );

		if( $key = $context->config()->get( 'resource/recaptcha/sitekey' ) )
		{
			$content .= '
				<script type="text/javascript" nonce="' . $context->nonce() . '">
					document.addEventListener("DOMContentLoaded", () => {
						const forms = document.querySelectorAll(".aimeos.cms-page form")

						if(forms.length) {
							const script = document.createElement("script");

							script.src = "https://www.google.com/recaptcha/api.js?render=' . $key . '";
							script.nonce = "' . $context->nonce() . '";
							script.type = "text/javascript";
							script.defer = true;

							document.body.appendChild(script);
						}

						forms.forEach(el => {
							el.addEventListener("submit", ev => {
								ev.preventDefault();
								grecaptcha.ready(() => {
									grecaptcha.execute("' . $key . '", { action: "cmspage" }).then(token => {
										const input = document.createElement("input");
										input.name = "g-recaptcha-response";
										input.type = "hidden";
										input.value = token;

										ev.target.appendChild(input);
										ev.target.submit();
									});
								});
							});
						});
					});
				</script>
			';
		}

		return $content;
	}
}
