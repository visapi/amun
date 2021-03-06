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

namespace AmunService\Media\Api;

use Amun\Base;
use Amun\Exception;
use Amun\Module\ApiAbstract;
use PSX\Data\Message;

/**
 * Serve
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Serve extends ApiAbstract
{
	/**
	 * Outputs the raw media item
	 *
	 * @httpMethod GET
	 * @path /{mediaId}
	 * @nickname doServe
	 * @responseClass PSX_Data_Message
	 */
	public function doServe()
	{
		try
		{
			// get id
			$mediaId = $this->getUriFragments('mediaId');

			if(strlen($mediaId) == 36)
			{
				$media = $this->getHandler()->getOneByGlobalId($mediaId);
			}
			else
			{
				$media = $this->getHandler()->getOneById($mediaId);
			}

			// get media item
			if(!empty($media))
			{
				// remove caching header
				header_remove('Expires');
				header_remove('Last-Modified');
				header_remove('Cache-Control');
				header_remove('Pragma');

				// check right
				if(!empty($media['rightId']) && !$this->user->hasRightId($media['rightId']))
				{
					throw new Exception('Access not allowed');
				}

				// send header
				switch($media['mimeType'])
				{
					case 'application/octet-stream':
						header('Content-Type: ' . $media['mimeType']);
						header('Content-Disposition: attachment; filename="' . $media['name'] . '"');
						break;

					default:
						header('Content-Type: ' . $media['mimeType']);
						break;
				}

				// read content
				if($media['path'][0] == '/' || $media['path'][1] == ':')
				{
					// absolute path
					$path = $media['path'];
				}
				else
				{
					// relative path
					$path = $this->registry['media.path'] . '/' . $media['path'];
				}

				if(!is_file($path))
				{
					throw new Exception('File not found', 404);
				}

				$response = file_get_contents($path);

				// caching header
				$etag  = md5($response);
				$match = Base::getRequestHeader('If-None-Match');
				$match = $match !== false ? trim($match, '"') : '';

				header('Etag: "' . $etag . '"');

				if($match != $etag)
				{
					echo $response;
				}
				else
				{
					header('HTTP/1.1 304 Not Modified');
				}

				exit;
			}
			else
			{
				throw new Exception('Invalid media id');
			}
		}
		catch(\Exception $e)
		{
			$msg = new Message($e->getMessage(), false);

			$this->setResponse($msg, null, 404);
		}
	}
}

