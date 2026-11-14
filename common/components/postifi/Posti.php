<?php
namespace common\components\postifi;

use common\components\postifi\services\PostiServiceInterface;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Posti component
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Posti implements PostiInterface
{
    protected $_combined_id;

    protected $_order_no;
    protected $_service;

    protected $_size; //cm
    protected $_weight; //kg


    const METHOD_GET = 'get';
    const METHOD_POST = 'post';

    /**
     * Set API key
     * @param int $combined_id
     * @return $this
     */
    public function setCombinedId($combined_id)
    {
        $this->_combined_id = $combined_id;
        return $this;
    }

    /**
     * Set order no
     * @param int|string $order_no
     * @return $this
     */
    public function setOrderNo($order_no)
    {
        $this->_order_no = $order_no;
        return $this;
    }

    /**
     * Get posti service
     * @return PostiServiceInterface
     */
    public function getService()
    {
        return $this->_service;
    }

    /**
     * Set posti service
     * @param PostiServiceInterface $service
     * @return $this
     */
    public function setService(PostiServiceInterface $service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * Set parcel size
     * @param double|int $width
     * @param double|int $height
     * @param double|int $length
     * @return $this
     */
    public function setSize($width, $height, $length)
    {
        $this->_size = [$width, $height, $length];
        return $this;
    }

    /**
     * Set parcel weight
     * @param double|int $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->_weight = $weight;
        return $this;
    }

    public function getPrice()
    {
        return 100;
    }

    public function postShipment()
    {
        $data = [
            'pdfConfig' => $this->getPdfConfig(),
            'shipment' => [
//                'orderNo' => $this->_order_no,
                'sender' => [
                    'quickId' => '1',
//                    'name' => 'Mad Wave Europe Oy',
//                    'city' => 'VILLÄHDE',
//                    'country' => 'FI'
                ],
//                'senderPartners' => [
//                    [
//                        "id" => "1",
//                        "custNo" => "CUSTNO"
//                    ]
//                ],
                "receiver" => [
                    "name" => "Adnrey Malkov",
                    "address1" => "Lepotie 2",
                    "zipcode" => "15540",
                    "city" => "VILLÄHDE",
                    "country" => "FI",
                    "phone" => "0445295591",
                    "email" => "info@madwave.eu",
                ],
                'service' => [
                    'id' => $this->getService()->getId()
                ],
                'parcels' => [
                    [
                        'copies' => '1',
                        'weight' => '1.5',
//                        'contents' => "important things",
//                        "valuePerParcel" => true
                    ]
                ],
            ]
        ];

//        return Json::encode($this->request(self::METHOD_POST, '/shipments', $data));
    }

    public function request($method, $url_path, $data = null)
    {
        if (!in_array($method, $this::availableMethods())) {
            throw new InvalidParamException('Parameter $method is invalid');
        }

        $client = new Client();

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod($method)
            ->setUrl('https://api.unifaun.com/rs-extapi/v1' . $url_path)
            ->setHeaders(['Authorization' => 'Bearer ' . $this->_combined_id])
            ->setData($data)
            ->send();

        if ($response->isOk) {
            return $response->data;
        }

        throw new \Exception(Json::encode([$response->statusCode, $response->data]));
    }

    protected function availableMethods()
    {
        return [self::METHOD_GET, self::METHOD_POST];
    }

    public function getPdfConfig()
    {
        return [
            "target4XOffset" => 0,
            "target2YOffset" => 0,
            "target1Media" => "laser-ste",
            "target1YOffset" => 0,
            "target3YOffset" => 0,
            "target2Media" => "laser-a4",
            "target4YOffset" => 0,
            "target4Media" => null,
            "target3XOffset" => 0,
            "target3Media" => null,
            "target1XOffset" => 0,
            "target2XOffset" => 0
        ];
    }
}