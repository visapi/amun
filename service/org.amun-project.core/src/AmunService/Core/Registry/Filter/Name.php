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

namespace AmunService\Core\Registry\Filter;

use PSX\FilterAbstract;

/**
 * Name
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Name extends FilterAbstract
{
	public function apply($value)
	{
		$len = strlen($value);

		for($i = 0; $i < $len; $i++)
		{
			$ascii = ord($value[$i]);

			# alpha (A - Z / a - z / 0 - 9 / _ . -)
			if(($ascii >= 0x41 && $ascii <= 0x5A) || ($ascii >= 0x61 && $ascii <= 0x7A) || ($ascii >= 0x30 && $ascii <= 0x39) || $ascii == 0x5F || $ascii == 0x2E || $ascii == 0x2D)
			{
				// valid sign
			}
			else
			{
				return false;
			}
		}

		return true;
	}

	public function getErrorMsg()
	{
		return '%s can only contain (A-Z a-z 0-9 _ .)';
	}
}

