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

    public function add(UploadedFile $picture, ?string $folder = '')
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
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            default:
                throw new Exception('Le format de l\'image n\'est pas reconnu');
        }

        // On crée le chemin complet de destination
        $path = $this->params->get('pictures_directory') . $folder;

        // On crée le dossier de destination s'il n'existe pas
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        // On stocke l'image
        imagewebp($picture_source, $path . '/' . $fichier);

        // Retourner le chemin relatif
        return 'assets/uploads/' . $folder . '/' . $fichier;
    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        $path = $this->params->get('pictures_directory') . $folder;

        if ($fichier !== 'defaut.webp') {
            $success = false;

            $mini = $path . '/mini/' . $width . '-' . $height . '-' . $fichier;

            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;

            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }
            return $success;
        }
        return false;
    }
}
