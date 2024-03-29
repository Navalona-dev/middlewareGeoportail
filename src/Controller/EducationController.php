<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\EducationRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\CreateMediaObjectAction;
use App\Service\EducationService;
use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EducationController extends AbstractController
{
    private $pathImage = null;
    private $pathImageEducation = null;
    private $pathPublic = null;
    private $pathForNamePhotoEducation = null;
    private $kernelInterface;

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface) {
        $this->pathImage = $params->get('base_url') . $params->get('pathPublic') . "education/";
        $this->pathImageEducation = $params->get('pathImageEducation');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoEducation = $params->get('pathForNamePhotoEducation');
        $this->kernelInterface = $kernelInterface;
    }

    /**
     * @Route("/api/education/add", name="education_add", methods={"POST"})
     */
    public function create(Request $request, EducationService $educationService)
    {    
       
        // $data = json_decode($request->getContent(), true);
        $response = new Response();

        try {
            $data = [];
            $data['nom' ] = $request->get('nom');
            $data['indicatif' ] = $request->get('indicatif');
            $data['categorie' ] = $request->get('categorie');
            $data['localite' ] = $request->get('localite');
            $data['sourceInformation' ] = $request->get('sourceInformation');
            $data['district' ] = $request->get('district');
            $data['sousCategorie' ] = $request->get('sousCategorie');
            $data['communeTerrain' ] = $request->get('communeTerrain');
            $data['numeroSequence' ] = $request->get('numeroSequence');
            $data['codeProduit' ] = $request->get('codeProduit');
            $data['codeCommune' ] = $request->get('codeCommune');
            $data['latitude' ] = $request->get('latitude');
            $data['longitude' ] = $request->get('longitude');
            $data['modeAcquisitionInformation' ] = $request->get('modeAcquisitionInformation');
            $data['infoSupplementaire' ] = [];
            $data['infoSupplementaire' ]['etat'] = $request->get('etat');
            $data['infoSupplementaire' ]['fonctionnel'] = $request->get('fonctionnel');
            $data['infoSupplementaire' ]['causeNonFonctinel'] = $request->get('causeNonFonctinel');
            $data['infoSupplementaire' ]['existenceCantine'] = $request->get('existenceCantine');
            $data['infoSupplementaire' ]['nombreEnseignant'] = $request->get('nombreEnseignant');
            $data['infoSupplementaire' ]['nombreEleve'] = $request->get('nombreEleve');
            
            $uploadedFile1 = $request->files->get('photo1');
            $uploadedFile2 = $request->files->get('photo2');
            $uploadedFile3 = $request->files->get('photo3');

            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                
                $directory1 = $this->pathImageEducation . "photo1/";
                $directoryPublic = $this->kernelInterface->getProjectDir().$this->pathPublic . "education/photo1/";
                
                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = $name_temp.".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                move_uploaded_file($tmpPathName1, $directoryPublic.$nomPhoto1);
                
                $data['photo1'] = $this->pathForNamePhotoEducation."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageEducation . "photo2/";
                $directoryPublic = $this->kernelInterface->getProjectDir().$this->pathPublic . "education/photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = $name_temp2.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                move_uploaded_file($tmpPathName2, $directoryPublic.$nomPhoto2);

                $data['photo2'] = $this->pathForNamePhotoEducation."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageEducation . "photo3/";
                $directoryPublic = $this->kernelInterface->getProjectDir().$this->pathPublic . "education/photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = $name_temp3.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                move_uploaded_file($tmpPathName3, $directoryPublic.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoEducation."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
            }
            
            $idInfra = $educationService->addInfrastructureEducation($data);
            
            if ($idInfra != false) {
                // add situation et etat
                $idEtat = $educationService->addInfrastructureEducationEtat($idInfra, $data);

                $idSituation = $educationService->addInfrastructureEducationSituation($idInfra, $data);

                $idDonneAnnexe = $educationService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
                
                /**
                 * Administrative data
                 */
                //Foncier
                $data['statut'] = $request->get('statutFoncier');
                $data['numeroReference'] = $request->get('numeroReferenceFoncier');
                $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');
                $dateInformationFoncier = new \DateTime($request->get('dateInformationFoncier'));
                $dateInformationFoncier->format('Y-m-d H:i:s');
                $data['dateInformationFoncier'] = $dateInformationFoncier;
                $data['sourceInformationFoncier'] = $request->get('sourceInformationFoncier');
                $data['modeAcquisitionInformationFoncier'] = $request->get('modeAcquisitionInformationFoncier');

                $idFoncier = $educationService->addInfrastructureEducationFoncier($idInfra, $data);

                //Travaux 
                $data['objetTravaux'] = $request->get('objetTravaux');
                $data['consistanceTravaux'] = $request->get('consistanceTravaux');
                $data['maitreOuvrageTravaux'] = $request->get('maitreOuvrageTravaux');
                $data['maitreOuvrageDelegueTravaux'] = $request->get('maitreOuvrageDelegueTravaux');
                $data['maitreOeuvreTravaux'] = $request->get('maitreOeuvreTravaux');
                $data['idControleSurveillanceTravaux'] = $request->get('idControleSurveillanceTravaux');//idControleSurveillance
                $data['modePassationTravaux'] = $request->get('modePassationTravaux');
                $data['porteAppelOffreTravaux'] = $request->get('porteAppelOffreTravaux');
                $data['montantTravaux'] = $request->get('montantTravaux');
                $data['numeroContratTravaux'] = $request->get('numeroContratTravaux');
                
                $dateContratTravaux = new \DateTime($request->get('dateContratTravaux'));
                $dateContratTravaux->format('Y-m-d H:i:s');
                $data['dateContratTravaux'] = $dateContratTravaux;

                $dateOrdreServiceTravaux = new \DateTime($request->get('dateOrdreServiceTravaux'));
                $dateOrdreServiceTravaux->format('Y-m-d H:i:s');

                $data['dateOrdreServiceTravaux'] = $dateOrdreServiceTravaux;
                $data['idTitulaireTravaux'] = $request->get('idTitulaireTravaux');//idTitulaire
                $data['resultatTravaux'] = $request->get('resultatTravaux');
                $data['motifRuptureContratTravaux'] = $request->get('motifRuptureContratTravaux');
                
                $dateReceptionProvisoireTravaux = new \DateTime($request->get('dateReceptionProvisoireTravaux'));
                $dateReceptionProvisoireTravaux->format('Y-m-d H:i:s');

                $data['dateReceptionProvisoireTravaux'] = $dateReceptionProvisoireTravaux;
                $dateReceptionDefinitiveTravaux = new \DateTime($request->get('dateReceptionDefinitiveTravaux'));
                $dateReceptionDefinitiveTravaux->format('Y-m-d H:i:s');

                $data['dateReceptionDefinitiveTravaux'] = $dateReceptionDefinitiveTravaux;
                $data['ingenieurReceptionProvisoireTravaux'] = $request->get('ingenieurReceptionProvisoireTravaux');
                $data['ingenieurReceptionDefinitiveTravaux'] = $request->get('ingenieurReceptionDefinitiveTravaux');
               
                $dateInformationTravaux = new \DateTime($request->get('dateInformationTravaux'));
                $dateInformationTravaux->format('Y-m-d H:i:s');

                $data['dateInformationTravaux'] = $dateInformationTravaux;
                $data['sourceInformationTravaux'] = $request->get('sourceInformationTravaux');
                $data['modeAcquisitionInformationTravaux'] = $request->get('modeAcquisitionInformationTravaux');
                
                $idTravaux = $educationService->addInfrastructureEducationTravaux($idInfra, $data);
                // Fournitures
                $data['objetContratFourniture'] = $request->get('objetContratFourniture');
                $data['consistanceContratFourniture'] = $request->get('consistanceContratFourniture');
                $data['materielsFourniture'] = $request->get('materielsFourniture');
                $data['entiteFourniture'] = $request->get('entiteFourniture');
                $data['modePassationFourniture'] = $request->get('modePassationFourniture');
                $data['porteAppelOffreFourniture'] = $request->get('porteAppelOffreFourniture');
                $data['montantFourniture'] = $request->get('montantFourniture');
                $data['idTitulaireFourniture'] = $request->get('idTitulaireFourniture');
                $data['numeroContratFourniture'] = $request->get('numeroContratFourniture');
                
                $dateContratFourniture = new \DateTime($request->get('dateContratFourniture'));
                $dateContratFourniture->format('Y-m-d H:i:s');


                $data['dateContratFourniture'] = $dateContratFourniture;
                $dateOrdreFourniture = new \DateTime($request->get('dateOrdreFourniture'));
                $dateOrdreFourniture->format('Y-m-d H:i:s');

                $data['dateOrdreFourniture'] = $dateOrdreFourniture;
                $data['resultatFourniture'] = $request->get('resultatFourniture');
                $data['raisonResiliationFourniture'] = $request->get('raisonResiliationFourniture');
                $data['ingenieurReceptionProvisoireFourniture'] = $request->get('ingenieurReceptionProvisoireFourniture');
                $data['ingenieurReceptionDefinitiveFourniture'] = $request->get('ingenieurReceptionDefinitiveFourniture');
                $dateReceptionProvisoireFourniture = new \DateTime($request->get('dateReceptionProvisoireFourniture'));
                $dateReceptionProvisoireFourniture->format('Y-m-d H:i:s');

                $data['dateReceptionProvisoireFourniture'] = $dateReceptionProvisoireFourniture;
                
                $dateReceptionDefinitiveFourniture = new \DateTime($request->get('dateReceptionDefinitiveFourniture'));
                $dateReceptionDefinitiveFourniture->format('Y-m-d H:i:s');

                $data['dateReceptionDefinitiveFourniture'] = $dateReceptionDefinitiveFourniture;
                
                $dateInformationFourniture = new \DateTime($request->get('dateInformationFourniture'));
                $dateInformationFourniture->format('Y-m-d H:i:s');

                $data['dateInformationFourniture'] = $dateInformationFourniture;

                $data['sourceInformationFourniture'] = $request->get('sourceInformationFourniture');
                $data['modeAcquisitionInformationFourniture'] = $request->get('modeAcquisitionInformationFourniture');

                $idFourniture = $educationService->addInfrastructureEducationFourniture($idInfra, $data);
                // Etudes
                $data['objetContratEtude'] = $request->get('objetContratEtude');
                $data['consistanceContratEtude'] = $request->get('consistanceContratEtude');
                $data['entiteEtude'] = $request->get('entiteEtude');
                $data['idTitulaireEtude'] = $request->get('idTitulaireEtude');
                $data['montantContratEtude'] = $request->get('montantContratEtude');
                $data['numeroContratEtude'] = $request->get('numeroContratEtude');
                $data['modePassationEtude'] = $request->get('modePassationEtude');
                $data['porteAppelOffreEtude'] = $request->get('porteAppelOffreEtude');
               
                $dateContratEtude = new \DateTime(trim($request->get('dateContratEtude')));

                $dateContratEtude->format('Y-m-d H:i:s');
                $data['dateContratEtude'] = $dateContratEtude;
                
                $dateOrdreServiceEtude = new \DateTime($request->get('dateOrdreServiceEtude'));
                $dateOrdreServiceEtude->format('Y-m-d H:i:s');

                $data['dateOrdreServiceEtude'] = $dateOrdreServiceEtude;

                if (null != $request->get('resultatPrestationEtude') && strlen($request->get('resultatPrestationEtude')) <= 20) {
                    $data['resultatPrestationEtude'] = $request->get('resultatPrestationEtude');
                } else {
                   throw new \Exception("Resultat prestation etude doit etre une chaine au maximal 20 caractere");
                }
                $data['motifRuptureContratEtude'] = $request->get('motifRuptureContratEtude');
                $dateInformationEtude = new \DateTime($request->get('dateInformationEtude'));
                $dateInformationEtude->format('Y-m-d H:i:s');

                $data['dateInformationEtude'] = $dateInformationEtude;
                $data['sourceInformationEtude'] = $request->get('sourceInformationEtude');
                $data['modeAcquisitionInformationEtude'] = $request->get('modeAcquisitionInformationEtude');

                $idEtude = $educationService->addInfrastructureEducationEtudes($idInfra, $data);
                /**
                 * End Administrative data
                */
            }
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "education created_successfull",
                'data' => $idInfra
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UnsufficientPrivilegeException->getMessage(),
            ]));
        /*} catch (ServerException $ServerException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ServerException->getMessage(),
            ]));*/
        } catch (NotNullConstraintViolationException $NotNullConstraintViolationException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }

        return $response;
    }

    /**
     * @Route("/api/infra/education/liste", name="education_list", methods={"GET"})
     */
    public function listeEducation(Request $request, EducationRepository $educationRepository)
    {    
        $response = new Response();

        try {

            $infrastructures = $educationRepository->getAllInfrastructuresEducation();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "education list_successfull",
                'pathImage' => $this->pathImage,
                'data' => $infrastructures
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UnsufficientPrivilegeException->getMessage(),
            ]));
        /*} catch (ServerException $ServerException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ServerException->getMessage(),
            ]));*/
        } catch (NotNullConstraintViolationException $NotNullConstraintViolationException) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }
        return $response;
    }
}
