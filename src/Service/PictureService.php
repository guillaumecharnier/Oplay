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

    public function delete(string $relativePath): bool
    {
        // Concaténer correctement le chemin du fichier
        $filePath = $this->params->get('kernel.project_dir') . '/public/' . $relativePath;

        // Vérifier si le fichier existe et est un fichier
        if (file_exists($filePath) && is_file($filePath)) {
            // Supprimer le fichier
            unlink($filePath);
            return true;
        } else {
            // Enregistrer un log si le fichier n'est pas trouvé ou n'est pas un fichier
            error_log('File not found or not a file: ' . $filePath);
        }

        return false;
    }
}
