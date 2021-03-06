<?php
/*
 * amun
 * A social content managment system based on the psx framework. For
 * the current version and informations visit <http://amun.phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of amun. amun is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * amun is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with amun. If not, see <http://www.gnu.org/licenses/>.
 */

namespace AmunService\Login\Application;

use Amun\Module\ApplicationAbstract;
use Amun\Exception;
use Amun\Captcha;
use Amun\Mail;
use AmunService\User\Account;
use PSX\DateTime;
use PSX\Filter;

/**
 * Register
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Register extends ApplicationAbstract
{
	public function onLoad()
	{
		parent::onLoad();

		if($this->user->hasRight('login_view'))
		{
			// captcha
			$captcha = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/core/captcha';

			$this->template->assign('captcha', $captcha);

			// add path
			$this->path->add('Register', $this->page->getUrl() . '/register');

			// template
			$this->htmlCss->add('login');
			$this->htmlJs->add('jquery');
		}
		else
		{
			throw new Exception('Access not allowed');
		}
	}

	public function onPost()
	{
		try
		{
			$name      = $this->post->name('string', array(new Filter\Length(3, 32)), 'name', 'Name');
			$identity  = $this->post->identity('string', array(new Filter\Length(3, 128), new Filter\Email()), 'email', 'Email');
			$pw        = $this->post->pw('string');
			$pwRepeat  = $this->post->pwRepeat('string');
			$longitude = $this->post->longitude('float');
			$latitude  = $this->post->latitude('float');
			$captcha   = $this->post->captcha('string');

			if(!$this->validate->hasError())
			{
				// check whether registration is enabled
				if(!$this->registry['login.registration_enabled'])
				{
					throw new Exception('Registration is disabled');
				}

				// compare pws
				if(strcmp($pw, $pwRepeat) != 0)
				{
					throw new Exception('Password ist not the same');
				}

				// check captcha if anonymous
				$captchaProvider = Captcha::factory($this->config['amun_captcha']);

				if(!$captchaProvider->verify($captcha))
				{
					throw new Exception('Invalid captcha');
				}

				// create account record
				$handler = $this->getHandler('AmunService\User\Account');

				$account = $handler->getRecord();
				$account->setGroupId($this->registry['core.default_user_group']);
				$account->setStatus(Account\Record::NOT_ACTIVATED);
				$account->setIdentity($identity);
				$account->setName($name);
				$account->setPw($pw);
				$account->setLongitude($longitude);
				$account->setLatitude($latitude);

				$account = $handler->create($account);

				if(isset($account->id))
				{
					// send activation mail
					$date = new DateTime('NOW', $this->registry['core.default_timezone']);

					$values = array(

						'account.name'     => $account->name,
						'account.identity' => $identity,
						'host.name'        => $this->base->getHost(),
						'register.link'    => $this->page->getUrl() . '/register/activate?token=' . $account->token,
						'register.date'    => $date->format($this->registry['core.format_date']),

					);

					$mail = new Mail($this->registry);
					$mail->send('LOGIN_REGISTRATION', $identity, $values);

					$this->template->assign('success', true);
				}
				else
				{
					throw new Exception('Your account was added for approval');
				}
			}
			else
			{
				throw new Exception($this->validate->getLastError());
			}
		}
		catch(\Exception $e)
		{
			$this->template->assign('name', htmlspecialchars($name));
			$this->template->assign('identity', htmlspecialchars($identity));

			$this->template->assign('error', $e->getMessage());
		}
	}
}

