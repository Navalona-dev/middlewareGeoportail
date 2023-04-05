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
use App\Service\RouteService;


use Doctrine\ORM\ORMInvalidArgumentException;
use App\Exception\PropertyVideException;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Exception\UnsufficientPrivilegeException;
use DateTime;
use Symfony\Component\HttpClient\Exception\ServerException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RouteController extends AbstractController
{
    private $pathImage = null;
    private $pathImageRoute = null;
    private $pathPublic = null;
    private $pathForNamePhotoRoute = null;

    public function __construct(ParameterBagInterface $params) {
        $this->pathImage = $params->get('base_url'). $params->get('pathPublic') . "route/";
        $this->pathImageRoute = $params->get('pathImageRoute');
        $this->pathPublic = $params->get('pathPublic');
        $this->pathForNamePhotoRoute = $params->get('pathForNamePhotoRoute');
    }

    /**
     * @Route("/api/route/add", name="route_add", methods={"POST"})
     */
    public function create(Request $request, RouteService $routeService)
    {    
        $response = new Response();

        try {

            $data = [];
            $data['region' ] = $request->get('region');
            $data['district' ] = $request->get('district');
            $data['commune' ] = $request->get('commune');
            $data['localite' ] = $request->get('localite');
            $data['rattache' ] = $request->get('rattache');
            $data['categorie' ] = $request->get('categorie');
            $data['sourceInformation' ] = $request->get('sourceInformation');
            $data['pkDebut' ] = $request->get('pkDebut');
            $data['pkFin' ] = $request->get('pkFin');
            $data['largeurHausse' ] = $request->get('largeurHausse');
            $data['largeurAccotement' ] = $request->get('largeurAccotement');
            $data['modeAcquisitionInformation' ] = $request->get('modeAcquisitionInformation');
            $data['gestionnaire'] = $request->get('gestionnaire');
            $data['modeGestion'] = $request->get('modeGestion');
            $data['latitude'] = $request->get('latitudePKDebut');
            $data['longitude'] = $request->get('longitudePKDebut');
            $data['axe'] = $request->get('axe');
            $data['structure'] = $request->get('structure');
            $data['procedureTravaux'] = $request->get('procedureTravaux');
            $data['precisionStructure'] = $request->get('precisionStructure');
            $data['precisionModeGestion'] = $request->get('precisionModeGestion');
            $data['etat'] = $request->get('etat');
            $data['fonctionnel'] = $request->get('fonctionnel');
            $data['causeNonFonctionel'] = $request->get('causeNonFonctionel');
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
            $data['structureOrnierage'] = $request->get('structureOrnierage');
            $data['accotementHasAccotementGauche'] = $request->get('accotementHasAccotementGauche');
            $data['accotementGauche'] = $request->get('accotementGauche');
            $data['accotementDroite'] = $request->get('accotementDroite');
            $data['accotementIsAccotementNonRevetu'] = $request->get('accotementIsAccotementNonRevetu');
            $data['accotementHasAccotementRevetue'] = $request->get('accotementHasAccotementRevetue');
            $data['accotementTypeRevetementAccotement'] = $request->get('accotementTypeRevetementAccotement');
            $data['accotementDegrationSurface'] = $request->get('accotementDegrationSurface');
            $data['accotementDentelleRive'] = $request->get('accotementDentelleRive');
            $data['accotementDenivellationChausseAccotement'] = $request->get('accotementDenivellationChausseAccotement');
            $data['accotementDestructionAffouillementAccotement'] = $request->get('accotementDestructionAffouillementAccotement');
            $data['accotementNonRevetueDeformationProfil'] = $request->get('accotementNonRevetueDeformationProfil');

            $dateInformationAccotement = new \DateTime($request->get('dateInformationAccotement'));
            $dateInformationAccotement->format('Y-m-d H:i:s');
            $data['dateInformationAccotement'] = $dateInformationAccotement;
            $data['sourceInformationAccotement' ] = $request->get('sourceInformationAccotement');
            $data['modeAcquisitionInformationAccotement' ] = $request->get('modeAcquisitionInformationAccotement');

            $data['fosseRevetuDegradationFosse'] = $request->get('fosseRevetuDegradationFosse');
            $data['fosseRevetuSectionBouche'] = $request->get('fosseRevetuSectionBouche');
            $data['fosseNonRevetuFosseProfil'] = $request->get('fosseNonRevetuFosseProfil');
            $data['fosseNonRevetuEncombrement'] = $request->get('fosseNonRevetuEncombrement');

            $dateInformationFosse = new \DateTime($request->get('dateInformationFosse'));
            $dateInformationFosse->format('Y-m-d H:i:s');
            $data['dateInformationFosse'] = $dateInformationFosse;
            $data['sourceInformationFosse' ] = $request->get('sourceInformationFosse');
            $data['modeAcquisitionInformationFosse' ] = $request->get('modeAcquisitionInformationFosse');
            
            
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
                $directory1 = $this->pathImageRoute . "photo1/";
                $directoryPublic = $this->pathPublic . "route/photo1/";

                $name_temp = hash('sha512', session_id().microtime($nomOriginal1));
                $nomPhoto1 = $name_temp.".".$uploadedFile1->getClientOriginalExtension();
                
                move_uploaded_file($tmpPathName1, $directory1.$nomPhoto1);
                move_uploaded_file($tmpPathName1, $directoryPublic.$nomPhoto1);

                $data['photo1'] = $this->pathForNamePhotoRoute."photo1/" .$nomPhoto1;
                $data['photoName1'] = $nomPhoto1;
            }
            
            if (null != $uploadedFile2) {
                $nomOriginal2 = $uploadedFile2->getClientOriginalName();
                $tmpPathName2 = $uploadedFile2->getPathname();
                $directory2 = $this->pathImageRoute . "photo2/";
                $directoryPublic = $this->pathPublic . "route/photo2/";

                $name_temp2 = hash('sha512', session_id().microtime($nomOriginal2));
                $nomPhoto2 = $name_temp2.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName2, $directory2.$nomPhoto2);
                move_uploaded_file($tmpPathName2, $directoryPublic.$nomPhoto2);
                
                $data['photo2'] = $this->pathForNamePhotoRoute."photo2/" .$nomPhoto2;
                $data['photoName2'] = $nomPhoto2;
            }

            if (null != $uploadedFile3) {
                $nomOriginal3 = $uploadedFile3->getClientOriginalName();
                $tmpPathName3 = $uploadedFile3->getPathname();
                $directory3 = $this->pathImageRoute . "photo3/";
                $directoryPublic = $this->pathPublic . "route/photo3/";

                $name_temp3 = hash('sha512', session_id().microtime($nomOriginal3));
                $nomPhoto3 = $name_temp3.".".$uploadedFile2->getClientOriginalExtension();
                move_uploaded_file($tmpPathName3, $directory3.$nomPhoto3);
                move_uploaded_file($tmpPathName3, $directoryPublic.$nomPhoto3);

                $data['photo3'] = $this->pathForNamePhotoRoute."photo3/" .$nomPhoto3;
                $data['photoName3'] = $nomPhoto3;
            }

            $idInfra = $routeService->addInfrastructureRoute($data);

            if ($idInfra != false) {
                // add situation et etat
                //$idEtat = $routeService->addInfrastructureRouteEtat($idInfra, $data);

                $idSituation = $routeService->addInfrastructureRouteSituation($idInfra, $data);

                $idSurface = $routeService->addInfrastructureRouteSurface($idInfra, $data);

                $idStructure = $routeService->addInfrastructureRouteStructure($idInfra, $data);

                $idAccotement = $routeService->addInfrastructureRouteAccotement($idInfra, $data);

                $idFosse = $routeService->addInfrastructureRouteFosse($idInfra, $data);
            

                /**
                 * Administrative data
                 */
                //Foncier
                $data['statut'] = $request->get('statutFoncier');
                $data['numeroReference'] = $request->get('numeroReferenceFoncier');
                $data['nomProprietaire'] = $request->get('nomProprietaireFoncier');

                $idFoncier = $routeService->addInfrastructureRouteFoncier($idInfra, $data);

                //Travaux 
                $data['objetTravaux'] = $request->get('objetTravaux');
                $data['consistanceTravaux'] = $request->get('consistanceTravaux');
                $data['modeRealisationTravaux'] = $request->get('modeRealisationTravaux');
                $data['maitreOuvrageTravaux'] = $request->get('maitreOuvrageTravaux');
                $data['maitreOuvrageDelegueTravaux'] = $request->get('maitreOuvrageDelegueTravaux');
                $data['idControleSurveillanceTravaux'] = $request->get('idControleSurveillanceTravaux');//idControleSurveillance
                $data['modePassationTravaux'] = $request->get('modePassationTravaux');
                $data['porteAppelOffreTravaux'] = $request->get('porteAppelOffreTravaux');
                $data['montantTravaux'] = $request->get('montantTravaux');
                $data['numeroContratTravaux'] = $request->get('numeroContratTravaux');
                $data['precisionConsistanceTravaux'] = $request->get('precisionConsistanceTravaux');
                
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

                $idTravaux = $routeService->addInfrastructureRouteTravaux($idInfra, $data);
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
                $data['bailleurFourniture'] = $request->get('bailleurFourniture');
                $idFourniture = $routeService->addInfrastructureRouteFourniture($idInfra, $data);
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
                $data['precisionConsistanceContratEtude'] = $request->get('precisionConsistanceContratEtude');
                $data['bailleurEtude'] = $request->get('bailleurEtude');
                $idEtude = $routeService->addInfrastructureRouteEtudes($idInfra, $data);
                /**
                 * End Administrative data
                */
                //$idDonneAnnexe = $routeService->addInfrastructureEducationDonneAnnexe($idInfra, $data);
            }

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route created_successfull"
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
     * @Route("/api/infra/route/liste", name="route_list", methods={"GET"})
     */
    public function listeRoute(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

            $routes = $routeService->getAllInfrastructuresRoute();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route list_successfull",
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

    /**
     * @Route("/api/infra/route/base/liste", name="route_base_list", methods={"GET"})
     */
    public function listeBaseRoute(Request $request, RouteService $routeService)
    {   
        $response = new Response();
        
        try {

            $baseRoutes = $routeService->getAllInfrastructuresBaseRoute();

            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route base list_successfull",
                'data' => $baseRoutes
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
     * @Route("/api/route/base/add", name="route_base_add", methods={"POST"})
     */
    public function createBaseRoute(Request $request, RouteService $routeService)
    {    
        $response = new Response();
        
        try {

            $data = json_decode($request->getContent(), true);
        
            $multipleCoordonne = "";
            if (count($data['localisation']) > 0) {
                
                foreach ($data['localisation'] as $key => $value) {
                    if (count($data['localisation']) - 1 == $key) {
                        $multipleCoordonne .= $value['latitude']." ".$value['longitude'];
                    } else {
                        $multipleCoordonne .= $value['latitude']." ".$value['longitude'].", ";
                    }
                    
                }
            }
            
            $result = $routeService->addInfrastructureBaseRoute($multipleCoordonne, $data['nom']);
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "route created_successfull"
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
