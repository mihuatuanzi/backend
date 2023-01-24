<?php

namespace App\Service;

use App\Interface\ObjectStorage;
use OSS\Core\OssException;
use OSS\OssClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AliOss implements ObjectStorage
{
    private const BUCKET = 'mihuatuanzi-backend';

    private OssClient $ossClient;

    public function __construct(
        ParameterBagInterface $parameterBag
    )
    {
        $accessKeyId = $parameterBag->get('env.oss.ali.access_key_id');
        $accessKeySecret = $parameterBag->get('env.oss.ali.access_key_secret');
        $endpoint = $parameterBag->get('env.oss.ali.endpoint');
        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
    }

    public function get(string $object): string
    {
        return $this->ossClient->getObject(self::BUCKET, $object);
    }

    /**
     * @throws OssException
     */
    public function put(string $object, string $file): void
    {
        $this->ossClient->uploadFile(self::BUCKET, $object, $file);
    }
}
