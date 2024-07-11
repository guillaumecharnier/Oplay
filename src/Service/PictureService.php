<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // On donne un nouveau nom à l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        // On récupère les infos de l'image
        $picture_infos = getimagesize($picture);
        if ($picture_infos === false) {
            throw new Exception('Le fichier n\'est pas une image');
        }

        // On vérifie le format de l'image
        switch ($picture_infos['mime']) {
            case 'image/png':
                $picture_source = \imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            default:
                throw new Exception('Le format de l\'image n\'est pas reconnu');
        }

        // On recadre l'image
        // On récupère les dimensions de l'image
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        // On vérifie l'orientation de l'image
        switch ($imageWidth <=> $imageHeight) {
            case -1: // portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case -1: // carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case -1: // paysage
                $squareSize = $imageHeight;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        // On crée une nouvelle image "vierge
        $resize_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resize_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        // On crée le dossier de destination s'il n'existe pas
        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        // On stocke l'image recadrée
        imagewebp($resize_picture, $path . '/mini/' . $width . '-' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        $path = $this->params->get('images_directory') . $folder;

        if ($fichier !== 'defaut.webp') {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/' . $width . '-' . $height . '-' . $fichier;

            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;

            if (file_exists($original)) {
                unlink($mini);
                $success = true;
            }
            return $success;
        }
    }
}
