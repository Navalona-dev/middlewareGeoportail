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
use App\Repository\RouteRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\CreateMediaObjectAction;
use App\Service\GareroutiereService;


use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use DateTime;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GareroutiereController extends AbstractController
{
    private $pathImage = null;
    private $pathImageGareroutiere = null;
    private $pathPublic = null;
    private $pathForNamePhotoGareroutiere = null;
    private $kernelInterface;

    public function __construct(ParameterBagInterface $params, KernelInterface  $kernelInterface) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . "Gareroutiere/";
        $this->pathImageGareroutiere = $params->get('pathImageGareroutiere');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoGareroutiere = $params->get('pathForNamePhotoGareroutiere');
        $this->kernelInterface = $kernelInterface;
    }

    /**
     * @Route("/api/gareroutiere/add", name="Gareroutiere_add", methods={"POST"})
     */
    public function create(Request $request, GareroutiereService $gareroutiereService)
    {    
        $response = new Response();
        $hasException = false;
        $idInfra = null;
        try {

            $data = [];
            $data['region' ] = $request->get('region');
            $data['district' ] = $request->get('district');
            $data['communeTerrain' ] = $request->get('commune');
            $data['nom' ] = $request->get('nom');
            $data['localite' ] = $request->get('localite');
            $data['code' ] = $request->get('code');
            $data['sourceInformation' ] = $request->get('sourceInformation');
            $data['modeAcquisitionInformation' ] = $request->get('modeAcquisitionInformation');
            $data['categorie' ] = $request->get('categorie');
            $data['capaciteVoitureAccueillies'] = $request->get('capaciteVoitureAccueillies');
            $data['categoriePrecision'] = $request->get('categoriePrecision');
            $data['latitude'] = $request->get('latitude');
            $data['longitude'] = $request->get('longitude');
            

            // Situation
            $data['etat'] = $request->get('etat');
            $data['fonctionnel'] = $request->get('fonctionnel');
            $data['motif'] = $request->get('motifNonFonctionel');
            $data['sourceInformationSituation' ] = $request->get('sourceInformationSituation');
            $data['modeAcquisitionInformationSituation' ] = $request->get('modeAcquisitionInformationSituation');
           

            // Data collecte
            $data['etatParking'] = $request->get('etatParking');
            $data['revetementParking'] = $request->get('revetementParking');
            $data['sourceInformationData'] = $request->get('sourceInformationData');
            $data['modeAcquisitionInformationData' ] = $request->get('modeAcquisitionInformationData');
            $data['etatGlobalAccessoires' ] = $request->get('etatGlobalAccessoires');
            
            
            /* $data['structure'] = $request->get('structure');
            $data['procedureTravaux'] = $request->get('procedureTravaux');
            $data['precisionStructure'] = $request->get('precisionStructure');
            $data['precisionModeGestion'] = $request->get('precisionModeGestion');
           
            
            
            $data['surfaceRevetement'] = $request->get('surfaceRevetement');
            $data['surfaceNidPoule'] = $request->get('surfaceNidPoule');
            $data['surfaceArrachement'] = $request->get('surfaceArrachement');
            $data['surfaceRessuage'] = $request->get('surfaceRessuage');
            $data['surfaceFissureJoint'] = $request->get('surfaceFissureJoint');
            $data['surfaceNonRevetuTraverse'] = $request->get('surfaceNonRevetuTraverse');
            $data['surfaceBourbier'] = $request->get('surfaceBourbier');
            $data['surfaceTeteChat'] = $request->get('surfaceTeteChat');
            $data['structureFissure'] = $request->get('structureFissure');
            $data['structureFaiencage'] = $request->get('structureFaiencage');
            $data['structureNidPouleStructure'] = $request->get('structureNidPouleStructure');
            $data['structureDeformation'] = $request->get('structureDeformation');
            $data['structureTeteOndule'] = $request->get('structureTeteOndule');
            $data['structureRavines'] = $request->get('structureRavines');
            //$data['structureOrnierage'] = $request->get('structureOrnierage');
            //$data['accotementHasAccotementGauche'] = $request->get('accotementHasAccotementGauche');
            $data['accotement'] = $request->get('accotement');
            //$data['accotementIsAccotementNonRevetu'] = $request->get('accotementIsAccotementNonRevetu');
            $data['accotementRevetue'] = $request->get('accotementRevetue');
            $data['accotementTypeRevetementAccotement'] = $request->get('accotementTypeRevetementAccotement');
            $data['accotementDegrationSurface'] = $request->get('accotementDegrationSurface');
            $data['accotementDentelleRive'] = $request->get('accotementDentelleRive');
            $data['accotementPrecisionTypeAccotement'] = $request->get('accotementPrecisionTypeAccotement');

            $data['accotementDenivellationChausseAccotement'] = $request->get('accotementDenivellationChausseAccotement');
            $data['accotementDestructionAffouillementAccotement'] = $request->get('accotementDestructionAffouillementAccotement');
            $data['accotementNonRevetueDeformationProfil'] = $request->get('accotementNonRevetueDeformationProfil');

            $dateInformationAccotement = new \DateTime($request->get('dateInformationAccotement'));
            $dateInformationAccotement->format('Y-m-d H:i:s');
            $data['dateInformationAccotement'] = $dateInformationAccotement;
            $data['sourceInformationAccotement' ] = $request->get('sourceInformationAccotement');
            $data['modeAcquisitionInformationAccotement' ] = $request->get('modeAcquisitionInformationAccotement');

            $data['fosseRevetu'] = $request->get('fosseRevetu');
            $data['fosseRevetuDegradationFosse'] = $request->get('fosseRevetuDegradationFosse');
            $data['fosseRevetuSectionBouche'] = $request->get('fosseRevetuSectionBouche');
            $data['fosseNonRevetuFosseProfil'] = $request->get('fosseNonRevetuFosseProfil');
            $data['fosseNonRevetuEncombrement'] = $request->get('fosseNonRevetuEncombrement');

            $dateInformationFosse = new \DateTime($request->get('dateInformationFosse'));
            $dateInformationFosse->format('Y-m-d H:i:s');
            $data['dateInformationFosse'] = $dateInformationFosse;
            $data['sourceInformationFosse' ] = $request->get('sourceInformationFosse');
            $data['modeAcquisitionInformationFosse' ] = $request->get('modeAcquisitionInformationFosse');
            $data['coteFosse'] = $request->get('coteFosse');*/
            
            
            $uploadedFile1 = $request->files->get('photo1');
            $uploadedFile2 = $request->files->get('photo2');
            $uploadedFile3 = $request->files->get('photo3');
            $data['photo1'] = null;
            $data['photo2'] = null;
            $data['photo3'] = null;
            $data['photoName1'] = null;
            $data['photoName2'] = null;
            $data['photoName3'] = null;
            if (null != $uploadedFile1) {
                $nomOriginal1 = $uploadedFile1->getClientOriginalName();
                $tmpPathName1 = $uploadedFile1->getPathname();
                $directory1 = $this->pathImageGareroutiere . "photo1/";
                $directoryPublic = $this->kernelInterface->getProjectDir().$this->pathPublic . "route/photo1/";

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = $name_temp.".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                move_uploaded_file($tmpPathName1, $directoryPublic.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoGareroutiere."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageGareroutiere . "photo2/";
                $directoryPublic = $this->kernelInterface->getProjectDir().$this->pathPublic . "route/photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = $name_temp2.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                move_uploaded_file($tmpPathName2, $directoryPublic.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoGareroutiere."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageGareroutiere . "photo3/";
                $directoryPublic = $this->kernelInterface->getProjectDir().$this->pathPublic . "route/photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = $name_temp3.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                move_uploaded_file($tmpPathName3, $directoryPublic.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoGareroutiere."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
            }

            $idInfra = $gareroutiereService->addInfrastructure($data);

            if ($idInfra != false) {
                // add situation et etat
                //$idEtat = $gareroutiereService->addInfrastructureRouteEtat($idInfra, $data);

                $idEtat = $gareroutiereService->addInfrastructureSituation($idInfra, $data);

                $idDataCollected = $gareroutiereService->addInfrastructureDonneCollecte($idInfra, $data);

                /*$idStructure = $gareroutiereService->addInfrastructureRouteStructure($idInfra, $data);

                $idAccotement = $gareroutiereService->addInfrastructureRouteAccotement($idInfra, $data);

                $idFosse = $gareroutiereService->addInfrastructureRouteFosse($idInfra, $data);*/
            

                /**
                 * Administrative data
                 */
                //Foncier
                $data['statut'] = $request->get('statutFoncier');
                $data['numeroReference'] = $request->get('numeroReferenceFoncier');
                $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');
                $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');
                $data['sourceInformationFoncier'] = $request->get('sourceInformationFoncier');
                $data['modeAcquisitionInformationFoncier'] = $request->get('modeAcquisitionInformationFoncier');
                $idFoncier = $gareroutiereService->addInfrastructureRouteFoncier($idInfra, $data);

                //Travaux 
                $data['objetTravaux'] = $request->get('objetTravaux');
                $data['consistanceTravaux'] = $request->get('consistanceTravaux');
                //$data['modeRealisationTravaux'] = $request->get('modeRealisationTravaux');
                $data['maitreOuvrageTravaux'] = $request->get('maitreOuvrageTravaux');
                $data['maitreOuvrageDelegueTravaux'] = $request->get('maitreOuvrageDelegueTravaux');
                $data['idControleSurveillanceTravaux'] = $request->get('idControleSurveillanceTravaux');//idControleSurveillance
                $data['modePassationTravaux'] = $request->get('modePassationTravaux');
                $data['porteAppelOffreTravaux'] = $request->get('porteAppelOffreTravaux');
                $data['montantTravaux'] = $request->get('montantTravaux');
                $data['numeroContratTravaux'] = $request->get('numeroContratTravaux');
                //$data['precisionConsistanceTravaux'] = $request->get('precisionConsistanceTravaux');
                
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
                $data['dateInformationTravaux'] = new \DateTime();
                $data['sourceInformationTravaux'] = $request->get('sourceInformationTravaux');
                $data['modeAcquisitionInformationTravaux'] = $request->get('modeAcquisitionInformationTravaux');
                $data['bailleurTravaux'] = $request->get('bailleurTravaux');

                $idTravaux = $gareroutiereService->addInfrastructureTravaux($idInfra, $data);
                // Fournitures
                /*$data['objetContratFourniture'] = $request->get('objetContratFourniture');
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
                $data['bailleurFourniture'] = $request->get('bailleurFourniture');
                $idFourniture = $gareroutiereService->addInfrastructureRouteFourniture($idInfra, $data);*/
                // Etudes
                $data['objetContratEtude'] = $request->get('objetContratEtude');
                $data['consistanceContratEtude'] = $request->get('consistanceContratEtude');
                $data['entiteEtude'] = $request->get('entiteEtude');
                $data['idTitulaireEtude'] = $request->get('idTitulaireEtude');
                $data['montantContratEtude'] = $request->get('montantContratEtude');
                $data['numeroContratEtude'] = $request->get('numeroContratEtude');
                $data['modePassationEtude'] = $request->get('modePassationEtude');
                $data['porteAppelOffreEtude'] = $request->get('porteAppelOffreEtude');

                $dateContratEtude = new \DateTime($request->get('dateContratEtude'));
                $dateContratEtude->format('Y-m-d H:i:s');

                $data['dateContratEtude'] = $dateContratEtude;

                $dateOrdreServiceEtude = new \DateTime($request->get('dateOrdreServiceEtude'));
                $dateOrdreServiceEtude->format('Y-m-d H:i:s');

                $data['dateOrdreServiceEtude'] = $dateOrdreServiceEtude;
                $data['resultatPrestationEtude'] = $request->get('resultatPrestationEtude');
                /*if (null != $request->get('resultatPrestationEtude') && strlen($request->get('resultatPrestationEtude')) <= 20) {
                    $data['resultatPrestationEtude'] = $request->get('resultatPrestationEtude');
                } else {
                   throw new \Exception("Resultat prestation etude doit etre une chaine au maximal 20 caractere");
                }*/
                
                $data['motifRuptureContratEtude'] = $request->get('motifRuptureContratEtude');
                
                $dateInformationEtude = new \DateTime($request->get('dateInformationEtude'));
                $dateInformationEtude->format('Y-m-d H:i:s');
                
                $data['dateInformationEtude'] = $dateInformationEtude;
                $data['sourceInformationEtude'] = $request->get('sourceInformationEtude');
                $data['modeAcquisitionInformationEtude'] = $request->get('modeAcquisitionInformationEtude');
                $data['precisionConsitanceContratEtude'] = $request->get('precisionConsitanceContratEtude');
                $data['bailleurEtude'] = $request->get('bailleurEtude');
                $idEtude = $gareroutiereService->addInfrastructureEtudes($idInfra, $data);
                /**
                 * End Administrative data
                */
                //$idDonneAnnexe = $gareroutiereService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Gare routiere created_successfull"
            ]));

            $response->headers->set('Content-Type', 'application/json');

        } catch (PropertyVideException $PropertyVideException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $PropertyVideException->getMessage()
            ]));
        } catch (UniqueConstraintViolationException $UniqueConstraintViolationException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $UniqueConstraintViolationException->getMessage()
            ]));
        } catch (MappingException $MappingException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $MappingException->getMessage()
            ]));
        } catch (ORMInvalidArgumentException $ORMInvalidArgumentException) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $ORMInvalidArgumentException->getMessage()
            ]));
        } catch (UnsufficientPrivilegeException $UnsufficientPrivilegeException) {
            $hasException = true;
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
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $NotNullConstraintViolationException->getMessage(),
            ]));
        } catch (\Exception $Exception) {
            $hasException = true;
            $response->setContent(json_encode([
                'status' => false,
                'message' => $Exception->getMessage(),
            ]));
        }

        if ($hasException) {// Clean database
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'infrastructure');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'situation');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'data');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'travaux');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'etude');
            /*
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'surface');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'structure');
            
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'accotement');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'fosse');
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'foncier');
           
            $gareroutiereService->cleanTablesByIdInfrastructure($idInfra, 'fourniture');*/
           
        }
        
        return $response;
    }

    /**
     * @Route("/api/infra/gareroutiere/liste", name="Gareroutiere_list", methods={"GET"})
     */
    public function listeDalot(Request $request, GareroutiereService $gareroutiereService)
    {    
        $response = new Response();
        
        try {

            $routes = $gareroutiereService->getAllInfrastructures();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "Gare routiere list_successfull",
                'pathImage' => $this->pathImage,
                'data' => $routes
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