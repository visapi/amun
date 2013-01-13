<?php
/*
 *  $Id: FriendsAbstract.php 635 2012-05-01 19:46:37Z k42b3.x@googlemail.com $
 *
 * amun
 * A social content managment system based on the psx framework. For
 * the current version and informations visit <http://amun.phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * Handles authentication against an LDAP server. The handler was tested with
 * the OpenDS (http://opends.java.net/) server. If you have problems with other 
 * LDAP implementations please contact us to increase the interoperability of 
 * the handler 
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   Amun
 * @package    Amun_Service_My
 * @version    $Revision: 635 $
 */
class AmunService_My_LoginHandler_Ldap extends AmunService_My_LoginHandlerAbstract
{
	const LDAP_HOST = 'localhost';
	const USER_DN   = ''; // user i.e. cn=Foo
	const USER_PW   = ''; // password
	const SALT_SIZE = 8;

	protected $res;

	public function __construct()
	{
		parent::__construct();

		$this->res = ldap_connect(self::LDAP_HOST);

		if(!$this->res)
		{
			throw new Amun_Exception('Ldap connection failed');
		}

		if(!ldap_bind($this->res, self::USER_DN, self::USER_PW))
		{
			throw new Amun_Exception('Could not bind Ldap');
		}
	}

	public function isValid($identity)
	{
		return true;
	}

	public function handle($identity, $password)
	{
		$result  = ldap_search($this->res, '', 'uid=' . $identity);
		$entries = ldap_get_entries($this->res, $result);
		$count   = isset($entries['count']) ? $entries['count'] : 0;

		if($count == 1)
		{
			$acc  = $entries[0];

			$mail = isset($acc['mail'][0]) ? $acc['mail'][0] : null;
			$name = isset($acc['givenname'][0]) ? $acc['givenname'][0] : null;
			$pw   = isset($acc['userpassword'][0]) ? $acc['userpassword'][0] : null;

			if(empty($mail))
			{
				throw new Amun_Exception('Mail not set');
			}

			if(empty($name))
			{
				throw new Amun_Exception('Given name not set');
			}

			if(empty($pw))
			{
				throw new Amun_Exception('User password not set');
			}

			if($this->comparePassword($pw, $password) === true)
			{
				$identity = $mail;
				$con      = new PSX_Sql_Condition(array('identity', '=', sha1(Amun_Security::getSalt() . $identity)));
				$userId   = Amun_Sql_Table_Registry::get('User_Account')->getField('id', $con);

				if(empty($userId))
				{
					// user doesnt exist so register a new user check whether 
					// registration is enabled
					if(!$this->registry['my.registration_enabled'])
					{
						throw new Amun_Exception('Registration is disabled');
					}

					// normalize name
					$name = $this->normalizeName($name);

					// create user account
					$handler = new AmunService_User_Account_Handler($this->user);

					$account = Amun_Sql_Table_Registry::get('User_Account')->getRecord();
					$account->setGroupId($this->registry['core.default_user_group']);
					$account->setStatus(AmunService_User_Account_Record::NORMAL);
					$account->setIdentity($identity);
					$account->setName($name);
					$account->setPw(Amun_Security::generatePw());

					$account = $handler->create($account);
					$userId  = $account->id;

					// if the id is not set the account was probably added to
					// the approval table
					if(!empty($userId))
					{
						$this->setUserId($userId);
					}
					else
					{
						throw new Amun_Exception('Could not create account');
					}
				}
				else
				{
					$this->setUserId($userId);
				}

				return true;
			}
			else
			{
				throw new AmunService_My_Login_InvalidPasswordException('Invalid password');
			}
		}
	}

	/**
	 * Compares the password from an attribute userPasssword with the given 
	 * password. Returns true if the password are equal. The ldap password 
	 * looks like: {SSHA}Gkau0n8WLjvQUOPVPET2xJo/2YlVHC1YaSk6FQ==
	 *
	 * @param string $ldapPassword
	 * @param string $password
	 * @return boolean
	 */
	protected function comparePassword($ldapPassword, $password)
	{
		$pos  = strpos($ldapPassword, '}');
		$type = substr($ldapPassword, 1, $pos - 1);
		$pw   = substr($ldapPassword, $pos + 1);
		$pw   = base64_decode($pw);
		$algo = 'md5';
		$salt = false;

		switch(strtoupper($type))
		{
			case 'SSHA':
				$salt = true;

			case 'SHA':
				$algo = 'sha1';
				break;

			case 'SMD5':
				$salt = true;

			case 'MD5':
				$algo = 'md5';
				break;
		}

		if($salt === true)
		{
			$salt = substr($pw, self::SALT_SIZE * -1);
			$pw   = substr($pw, 0, self::SALT_SIZE * -1);

			return strcasecmp(base64_encode($pw), base64_encode(hash($algo, $password . $salt, true))) === 0;
		}
		else
		{
			return strcasecmp(base64_encode($pw), base64_encode(hash($algo, $password, true))) === 0;
		}

		return false;
	}
}