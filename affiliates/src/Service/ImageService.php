<?php
declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    private const UPLOADS_PATH = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';

    private const ALLOWED_IMAGE_TYPES_MAP = [
        'image/jpeg' => '.jpg',
        'image/webp' => '.webp',
    ];

    public function moveImageToUploadsAndGetPath(UploadedFile $file): ?string
    {
        $name = $file->getClientOriginalName();
        $type = $file->getMimeType();
        $imageExt = self::ALLOWED_IMAGE_TYPES_MAP[$type] ?? null;

        if ($imageExt === null)
        {
            throw new InvalidArgumentException("File '$name' is not valid");
        }

        $fileName = uniqid('image', true) . $imageExt;
        return $this->moveFileToUploads($file, $fileName);
    }

    private function moveFileToUploads(UploadedFile $file, string $fileName): string
    {
        $clientFileName = $file->getClientOriginalName();
        $destPath = $this->getUploadPath($fileName);
        $srcPath = $file->getRealPath();

        if (!@move_uploaded_file($srcPath, $destPath))
        {
            throw new RuntimeException("Failed to uploads file $clientFileName");
        }

        return $fileName;
    }

    private function getUploadPath(string $fileName): string
    {
        $uploadPath = dirname(__DIR__, 2) . self::UPLOADS_PATH;
        if (!$uploadPath || !is_dir($uploadPath))
        {
            throw new RuntimeException('Invalid uploads path: ' . self::UPLOADS_PATH);
        }

        return $uploadPath . DIRECTORY_SEPARATOR . $fileName;
    }
}