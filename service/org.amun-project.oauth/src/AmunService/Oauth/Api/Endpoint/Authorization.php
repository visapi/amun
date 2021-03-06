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

namespace AmunService\Oauth\Api\Endpoint;

use Amun\Dependency;
use Amun\Exception;
use PSX\Filter;
use PSX\ModuleAbstract;

/**
 * Authorization
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Authorization extends ModuleAbstract
{
	/**
	 * Endpoint to redirect the client to the authentication page containing the
	 * oauth token
	 *
	 * @httpMethod GET
	 * @path /
	 * @nickname doAuthorization
	 * @parameter query oauth_token string
	 * @responseClass PSX_Data_ResultSet
	 */
	public function doAuthorization()
	{
		// get oauth token
		$oauthToken = $this->getInputGet()->oauth_token('string', array(new Filter\Length(40, 40), new Filter\Xdigit()));

		if(!$this->getValidate()->hasError())
		{
			// redirect to the auth page
			header('Location: ' . $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'login/auth?oauth_token=' . $oauthToken);
			exit;
		}
		else
		{
			throw new Exception($this->getValidate()->getLastError());
		}
	}
}

