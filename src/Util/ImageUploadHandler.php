<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 5/3/17
 * Time: 10:33 AM
 */

namespace App\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class ImageUploadHandler
{

    private $projectDir;
    private $storageType;

    private $qiniuAccessKey;
    private $qiniuSecretKey;
    private $qiniuBucket;


    const TYPE_FILESYSTEM = 'filesystem';
    const TYPE_QINIU = 'qiniu';


    /**
     * ImageUploadHandler constructor.
     * @param string $projectDir
     * @param int $storageType
     * @param string $qiniuAccessKey
     * @param string $qiniuSecretKey
     * @param string $qiniuBucket
     */
    public function __construct($projectDir, $storageType, $qiniuAccessKey = "", $qiniuSecretKey = "", $qiniuBucket = "")
    {
        $this->projectDir = $projectDir;
        $this->storageType = $storageType;
        $this->qiniuAccessKey = $qiniuAccessKey;
        $this->qiniuSecretKey = $qiniuSecretKey;
        $this->qiniuBucket = $qiniuBucket;
    }

    /**
     * @param UploadedFile $file
     * @param string $imagePath
     */
    public function handleImageUpload($file, $imagePath)
    {
        if ($this->storageType == self::TYPE_FILESYSTEM) {
            $this->handleFileSystemImageUpload($file, $imagePath);
        }
        if ($this->storageType == self::TYPE_QINIU) {
            $this->handleQiniuImageUpload($file, $imagePath);
        }
    }


    /**
     * @param UploadedFile $file
     * @param string $imagePath
     */
    public function handleFileSystemImageUpload($file, string $imagePath)
    {
        $pubDir = $this->projectDir . "/public/";
        if ($imagePath && ($imagePath{0} === "/")) {
            $imagePath = mb_substr($imagePath, 1);
        }

        $fullTargetFileName = $pubDir . $imagePath;
        $pathParts = pathinfo($fullTargetFileName);
        $file->move($pathParts['dirname'], $pathParts['basename']);
    }


    /** @var UploadManager */
    private $qiniuUploadManager = null;
    private $qiniuUploadToken = null;

    private function initQiniu()
    {
        if ($this->qiniuUploadToken && $this->qiniuUploadManager) {
            return;
        }
        $accessKey = $this->qiniuAccessKey;
        $secretKey = $this->qiniuSecretKey;
        $auth = new Auth($accessKey, $secretKey);
        $bucket = $this->qiniuBucket;
        $this->qiniuUploadToken = $auth->uploadToken($bucket);
        $this->qiniuUploadManager = new UploadManager();
    }

    /**
     * @param UploadedFile $file
     * @param string $fileKey
     * @inheritdoc
     */
    public function handleQiniuImageUpload($file, $fileKey)
    {
        $this->initQiniu();
        $filePath = $file->getRealPath();

        /** @noinspection PhpParamsInspection */
        list($ret, $err) = $this->qiniuUploadManager->putFile($this->qiniuUploadToken, $fileKey, $filePath);
        @unlink($filePath);
        if ($err !== null) {
            throw new \RuntimeException("Qiniu upload fail: " . json_encode($err));
        }
    }

}