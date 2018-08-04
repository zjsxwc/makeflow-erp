<?php

namespace App\Controller;


use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;



class ImageController extends Controller
{
    /**
     * @Route("/user/upload-images", name="upload_images")
     * @Method("POST")
     */
    public function uploadImagesAction(Request $request)
    {
        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();

        $storagePrefix = sprintf("upload-%s/", $this->getParameter("kernel.environment"));

        $em = $this->get("doctrine.orm.entity_manager");
        $cdnUrl = $this->getParameter("cdn_url");

        $uploadResult = [];
        $imageUploadHandler = $this->get("App\Util\ImageUploadHandler");

        /**  UploadedFile[]  $files */
        $files = $request->files->all();
        foreach ( $files as $file  )
        {
            /** @var UploadedFile $file */

            $mayAllowedImageExtension = $file->guessExtension();

            if (!in_array($mayAllowedImageExtension, [ 'jpeg', 'png', 'gif' ] ) ) {
                continue;
            }

            $targetFileName = $currentUser->getId()  . '-' . md5(uniqid()).'.'.$mayAllowedImageExtension;
            $imagePath = $storagePrefix.$targetFileName;

            try {
                $imageUploadHandler->handleImageUpload($file, $imagePath);
            } catch (\Exception $e) {
                continue;
            }

            $newImage = new Image();
            $newImage->setUserId($currentUser->getId());
            $newImage->setPath($imagePath);
            $newImage->setCreateTime(time());

            try {
                $em->persist($newImage);
            } catch (\Exception $e) {
                return $this->json([
                    "code" => $e->getCode(),
                    "message" => $e->getMessage()
                ]);
            }

            $uploadResult[] = $imagePath;
        }

        try {
            $em->flush();
        } catch (\Exception $e) {
            return $this->json([
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            ]);
        }

        $imageUrlList = [];
        foreach ($uploadResult as $oneUploadResult) {
            $imageUrlList[] = $cdnUrl . "/" .$oneUploadResult;
        }

        return $this->json([
            "code" => -1,
            "data" => [
                "imageUrlList" => $imageUrlList,
                "imageUrls" => $uploadResult,
                "cdnUrl" => $cdnUrl
            ]
        ]);
    }

}
