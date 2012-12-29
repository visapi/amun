<?php
/*
 *  $Id: GadgetAbstract.php 813 2012-07-11 18:18:45Z k42b3.x@googlemail.com $
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
 * Amun_Module_GadgetAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   Amun
 * @package    Amun_Module
 * @version    $Revision: 813 $
 */
abstract class Amun_Module_GadgetAbstract extends PSX_Module_ViewAbstract
{
	public function getDependencies()
	{
		$ct = new Amun_Dependency_Gadget($this->base->getConfig(), array(
			'gadget.id' => $this->location->getServiceId()
		));

		Amun_DataFactory::setContainer($ct);

		return $ct;
	}

	protected function getProvider($name = null)
	{
		$name = $name === null ? $this->service->namespace : $name;

		return Amun_DataFactory::getProvider($name);
	}

	protected function getTable($name = null)
	{
		return $this->getProvider($name)->getTable();
	}

	protected function getHandler($name = null)
	{
		return $this->getProvider($name)->getHandler();
	}
}
