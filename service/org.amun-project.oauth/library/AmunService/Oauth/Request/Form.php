<?php
/*
 *  $Id: Form.php 666 2012-05-12 22:10:25Z k42b3.x@googlemail.com $
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
 * AmunService_Oauth_Request_Form
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   Amun
 * @package    Amun_Oauth
 * @version    $Revision: 666 $
 */
class AmunService_Oauth_Request_Form extends Amun_Data_FormAbstract
{
	public function create()
	{
		throw new PSX_Data_Exception('You cant create a request record');
	}

	public function update($id)
	{
		throw new PSX_Data_Exception('You cant update a request record');
	}

	public function delete($id)
	{
		$record = Amun_Sql_Table_Registry::get('Oauth_Request')->getRecord($id);


		$form = new Amun_Form('DELETE', $this->url);


		$panel = new Amun_Form_Element_Panel('request', 'Request');


		$id = new Amun_Form_Element_Input('id', 'Id', $record->id);
		$id->setType('hidden');

		$panel->add($id);


		$status = new Amun_Form_Element_Input('status', 'Status', $record->status);
		$status->setType('text');
		$status->setDisabled(true);

		$panel->add($status);


		$callback = new Amun_Form_Element_Input('callback', 'Callback', $record->callback);
		$callback->setType('text');
		$callback->setDisabled(true);

		$panel->add($callback);


		$expire = new Amun_Form_Element_Input('expire', 'Expire', $record->expire);
		$expire->setType('text');
		$expire->setDisabled(true);

		$panel->add($expire);


		$date = new Amun_Form_Element_Input('date', 'Date', $record->date);
		$date->setType('text');
		$date->setDisabled(true);

		$panel->add($date);


		if($this->user->isAnonymous() || $this->user->hasInputExceeded())
		{
			$captcha = new Amun_Form_Element_Captcha('captcha', 'Captcha');
			$captcha->setSrc($this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/system/captcha');

			$panel->add($captcha);
		}


		$form->setContainer($panel);


		return $form;
	}
}
