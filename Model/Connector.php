<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

declare(strict_types=1);

namespace Tun2U\OrderMigration\Model;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Tun2U\OrderMigration\Helper\Data;

/**
 * Class OrderMigration
 */
class OrderMigration
{
	/**
	 * API request URL
	 */
	const API_REQUEST_URI = 'https://admin.tun2ustores.com/rest/V1/TUN2U/';

	/**
	 * Request timeout
	 */
	const TIMEOUT = 100.0;

	/**
	 * @var ResponseFactory
	 */
	private $responseFactory;

	/**
	 * @var ClientFactory
	 */
	private $clientFactory;

	/**
	 * @var Data
	 */
	private $helperData;

	/**
	 * OrderMigration constructor.
	 *
	 * @param ClientFactory $clientFactory
	 * @param ResponseFactory $responseFactory
	 */
	public function __construct(
		ClientFactory $clientFactory,
		ResponseFactory $responseFactory,
		Data $helperData
	) {
		$this->clientFactory = $clientFactory;
		$this->responseFactory = $responseFactory;
		$this->helperData = $helperData;
	}

	/**
	 * Do API request with provided params
	 *
	 * @param string $uriEndpoint
	 * @param array $params
	 * @param string $requestMethod
	 * @return Response
	 */
	public function doRequest(
		string $uriEndpoint,
		string $requestMethod = Request::HTTP_METHOD_GET,
		array $data = []
	) {
		/** @var Client $client */
		$client = $this->clientFactory->create(['config' => [
			'base_uri' => self::API_REQUEST_URI,
			'timeout'  => self::TIMEOUT
		]]);

		$params = [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->helperData->getAccessToken(),
				'Accept' => 'application/json',
				'Content-type' => 'application/json'
			]
		];

		if (!empty($data)) {
			$params['body'] = json_encode($data);
		}

		try {
			$res = $client->request(
				$requestMethod,
				$uriEndpoint,
				$params
			);

			$responseBody = $res->getBody();
			if ($responseBody) {
				$response = json_decode($responseBody->getContents());
			}
		} catch (GuzzleException $exception) {

			throw new Exception('[' . $exception->getCode() . '] ' . $exception->getMessage());

			/** @var Response $response */
			// $response = $this->responseFactory->create([
			// 	'status' => $exception->getCode(),
			// 	'reason' => $exception->getMessage()
			// ]);
		}

		return $response;
	}

	public function call($endpoint, $id = null, $searchCriteria = null)
	{
		$url = $endpoint;
		if ($id) {
			$url .= '/' . $id;
		}
		if ($searchCriteria) {
			$url .= '/?' . $searchCriteria;
		}

		$result = $this->doRequest($url);

		return $result;
	}

	public function send($endpoint, $method, $data)
	{
		$result = $this->doRequest($endpoint, $method, $data);

		return $result;
	}
}
