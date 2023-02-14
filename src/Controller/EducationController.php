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

class EducationController extends AbstractController
{
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

            $nomOriginal1 = $uploadedFile1->getClientOriginalName();
            $tmpPathName1 = $uploadedFile1->getPathname();
            $directory1 = $this->getParameter('pathImageEducation') . "photo1/";
            move_uploaded_file($tmpPathName1, $directory1.$tmpPathName1);
            $data['photo1'] = $nomOriginal1;

            $nomOriginal2 = $uploadedFile2->getClientOriginalName();
            $tmpPathName2 = $uploadedFile2->getPathname();
            $directory2 = $this->getParameter('pathImageEducation') . "photo2/";
            move_uploaded_file($tmpPathName2, $directory2.$tmpPathName2);
            $data['photo2'] = $nomOriginal2;
            
            $nomOriginal3 = $uploadedFile3->getClientOriginalName();
            $tmpPathName3 = $uploadedFile3->getPathname();
            $directory3 = $this->getParameter('pathImageEducation') . "photo3/";
            move_uploaded_file($tmpPathName3, $directory3.$tmpPathName3);
            $data['photo3'] = $nomOriginal3;
            
            dd($tmpPathName1, $tmpPathName1);
            
            
            
            $id = $educationService->addInfrastructureEducation($data);
            
            if ($id != false) {
                // add situation et etat
                $idEtat = $educationService->addInfrastructureEducationEtat($id, $data);

                $idSituation = $educationService->addInfrastructureEducationSituation($id, $data);

                $idDonneAnnexe = $educationService->addInfrastructureEducationDonneAnnexe($id, $data);
            }
            
            $response->setContent(json_encode([
                'code'  => Response::HTTP_OK,
                'status' => true,
                'message' => "education created_successfull",
                'data' => $id
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
