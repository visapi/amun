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

namespace AmunService\My;

use Amun\Option;
use AmunService\My\MyAbstract;

/**
 * SettingsAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
abstract class SettingsAbstract extends MyAbstract
{
	public function onLoad()
	{
		parent::onLoad();

		// options
		$settings = new Option('settings', $this->registry, $this->user, $this->page);
		$settings->add('my_view', 'Account', $this->page->getUrl() . '/settings');
		$settings->add('my_view', 'Security', $this->page->getUrl() . '/settings/security');
		$settings->add('my_view', 'Connection', $this->page->getUrl() . '/settings/connection');
		$settings->add('my_view', 'Application', $this->page->getUrl() . '/settings/application');
		$settings->load(array($this->page));

		$this->template->assign('optionsSettings', $settings);
	}
}

