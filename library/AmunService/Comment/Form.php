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

namespace AmunService\Comment;

use Amun\DataFactory;
use Amun\Data\FormAbstract;
use Amun\Form as AmunForm;
use Amun\Form\Element\Panel;
use Amun\Form\Element\Reference;
use Amun\Form\Element\Input;
use Amun\Form\Element\Textarea;
use Amun\Form\Element\Captcha;

/**
 * Form
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Form extends FormAbstract
{
	public function create($pageId = 0, $refId = 0)
	{
		$form = new AmunForm('POST', $this->url);


		$panel = new Panel('comment', 'Comment');


		if(empty($pageId))
		{
			$pageId = new Reference('pageId', 'Page ID');
			$pageId->setValueField('id');
			$pageId->setLabelField('title');
			$pageId->setSrc($this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/content/page');

			$panel->add($pageId);
		}
		else
		{
			$pageId = new Input('pageId', 'Page ID', $pageId);
			$pageId->setType('hidden');

			$panel->add($pageId);
		}


		if(empty($refId))
		{
			$refId = new Input('refId', 'Ref ID');
			$refId->setType('text');

			$panel->add($refId);
		}
		else
		{
			$refId = new Input('refId', 'Ref ID', $refId);
			$refId->setType('hidden');

			$panel->add($refId);
		}


		$text = new Textarea('text', 'Text');

		$panel->add($text);


		if($this->user->isAnonymous() || $this->user->hasInputExceeded())
		{
			$captcha = new Captcha('captcha', 'Captcha');
			$captcha->setSrc($this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/core/captcha');

			$panel->add($captcha);
		}


		$form->setContainer($panel);


		return $form;
	}

	public function update($id)
	{
		$record = $this->hm->getHandler('Comment')->getRecord($id);


		$form = new AmunForm('PUT', $this->url);


		$panel = new Panel('comment', 'Comment');


		$id = new Input('id', 'Id', $record->id);
		$id->setType('hidden');

		$panel->add($id);


		$text = new Textarea('text', 'Text', $record->text);

		$panel->add($text);


		if($this->user->isAnonymous() || $this->user->hasInputExceeded())
		{
			$captcha = new Captcha('captcha', 'Captcha');
			$captcha->setSrc($this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/core/captcha');

			$panel->add($captcha);
		}


		$form->setContainer($panel);


		return $form;
	}

	public function delete($id)
	{
		$record = $this->hm->getHandler('Comment')->getRecord($id);


		$form = new AmunForm('DELETE', $this->url);


		$panel = new Panel('comment', 'Comment');


		$id = new Input('id', 'Id', $record->id);
		$id->setType('hidden');

		$panel->add($id);


		$text = new Textarea('text', 'Text', $record->text);
		$text->setDisabled(true);

		$panel->add($text);


		if($this->user->isAnonymous() || $this->user->hasInputExceeded())
		{
			$captcha = new Captcha('captcha', 'Captcha');
			$captcha->setSrc($this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/core/captcha');

			$panel->add($captcha);
		}


		$form->setContainer($panel);


		return $form;
	}
}

