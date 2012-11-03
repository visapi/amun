<?php
/*
 *  $Id: authorization.php 690 2012-06-06 23:11:27Z k42b3.x@googlemail.com $
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
 * authorization
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   module
 * @package    api
 * @subpackage auth
 * @version    $Revision: 690 $
 */
class authorization extends PSX_ModuleAbstract
{
	protected $sql;
	protected $registry;
	protected $validate;
	protected $get;
	protected $post;

	public function getDependencies()
	{
		return new Amun_Dependency_Default();
	}

	public function onLoad()
	{
		// get oauth token
		$oauthToken = $this->get->oauth_token('string', array(new PSX_Filter_Length(40, 40), new PSX_Filter_Xdigit()));

		if(!$this->validate->hasError())
		{
			// redirect to the auth page
			header('Location: ' . $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'my.htm/auth?oauth_token=' . $oauthToken);

			exit;
		}
		else
		{
			throw new Amun_Exception($this->validate->getLastError());
		}
	}
}

