<?
// src/Controller/ImagesController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImagesController extends AbstractController
{
    public function displayImage($imageName)
    {
        $imagePath = '/home/qgis/projects/saisie/media/upload/tj_trajet_route/t_tj_01_infrastructure/' . $imageName;

        // Vérifiez si le fichier existe
        if (!file_exists($imagePath)) {
            throw $this->createNotFoundException('Image not found');
        }

        // Renvoyer la réponse avec le fichier image
        return new BinaryFileResponse($imagePath);
    }
}
