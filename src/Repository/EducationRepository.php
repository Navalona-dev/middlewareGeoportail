<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class EducationRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager("middleware");
    }

    public function addInfrastructureEducation($nom = null, $indicatif = null, $categorie = null, $localite = null, $sourceInformation = null, $modeAcquisitionInformation = null, $communeTerrain = null, $numeroSequence = null, $codeProduit = null, $codeCommune = null, $latitude = null, $longitude = null, $sousCategorie= null, $district = null, $photo1 = null, $photo2 = null, $photo3 = null, $photoName1 = null, $photoName2 = null, $photoName3 = null )
    {   
        $sourceInfo = pg_escape_string($sourceInformation);

        $conn = $this->entityManager->getConnection();

        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ec_01_infrastructure (nom, indicatif, categorie, localite, commune_terrain, date_information, source_Information, mode_acquisition_information, geom,  numero_sequence, code_produit, code_commune, sous_categorie, district, photo1, photo2, photo3, photo_name1, photo_name2, photo_name3 ) VALUES ('".$nom."', ".$conn->quote($indicatif).", '".$categorie."', '".$localite."', '".$communeTerrain."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."', ST_GeomFromText('POINT(" . $longitude . " " . $latitude . ")', 4326), '".$numeroSequence."', ".$codeProduit.", ".$codeCommune.", '".$sousCategorie."', '".$district."', '".$photo1."', '".$photo2."', '".$photo3."', '".$photoName1."', '".$photoName2."', '".$photoName3."')";
        
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEducationEtat($idInfrastructure = null, $etat = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ec_04_etat (id_infrastructure, etat, date_information, source_Information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$etat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEducationSituation($idInfrastructure = null, $fonctionnel = null, $raison, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ec_03_situation (id_infrastructure, fonctionnel, raison, date_information, source_Information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$fonctionnel."', '".$raison."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEducationDonneAnnexe($idInfrastructure = null, $existenceCantine = null, $nombreEnseignant, $nombreEleve = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ec_14_donnees_annexes (existence_cantine, nombre_enseignant, nombre_eleve, date_information, source_Information, mode_acquisition_infromation, id_infrastructure) VALUES ('".$existenceCantine."', ".intval($nombreEnseignant).", ".intval($nombreEleve).", '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."', ".intval($idInfrastructure).")";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }
    
    public function addInfrastructureEducationFoncier($idInfrastructure = null, $statutFoncier = null, $proprietaire = null, $referenceDossier = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sql = "INSERT into t_ec_05_foncier (id_infrastructure, statut_foncier, proprietaire, reference_dossier, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$statutFoncier."', '".$proprietaire."', '".$referenceDossier."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEducationTravaux($idInfrastructure = null, $objet = null, $consistanceTravaux = null, $maitreOuvrage = null, $maitreOuvrageDelegue = null, $maitreOeuvre = null, $idControleSurveillance = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $numeroContrat = null, $dateContrat = null, $dateOrdreService = null, $idTitulaire = null, $resultatTravaux = null, $motifRuptureContrat = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sql = "INSERT into t_ec_08_travaux (id_infrastructure, objet, consistance_travaux, maitre_ouvrage, maitre_ouvrage_delegue, maitre_oeuvre, id_controle_surveillance, mode_passation, porte_appel_offre, montant, numero_contrat, date_contrat, date_ordre_service, id_titulaire, resultat_travaux, motif_rupture_contrat, date_reception_provisoire, date_reception_definitive, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$objet."', '".$consistanceTravaux."', '".$maitreOuvrage."', '".$maitreOuvrageDelegue."', '".$maitreOeuvre."', '".$idControleSurveillance."', '".$modePassation."', '".$porteAppelOffre."', '".$montant."', '".$numeroContrat."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdreService->format("Y-m-d")."', '".$idTitulaire."', '".$resultatTravaux."', '".$motifRuptureContrat."', '".$dateReceptionProvisoire->format("Y-m-d")."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEducationFourniture($idInfrastructure = null, $objetContrat = null, $consistanceContrat = null, $materiels = null, $entite = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $idTitulaire = null, $numeroContrat = null, $dateContrat = null, $dateOrdre = null, $resultat = null, $motifRuptureContrat = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sql = "INSERT into t_ec_10_fourniture (id_infrastructure, objet, consistance_contrat, materiel_concerne, entite, id_titulaire, mode_passation, porte_appel_offre, montant, numero_contrat, date_contrat, date_ordre_service, resultat_service, motif_rupture_contrat, date_reception_provisoire, date_reception_definitive, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$objetContrat."', '".$consistanceContrat."', '".$materiels."', '".$entite."', '".$idTitulaire."', '".$modePassation."', '".$porteAppelOffre."', ".intval($montant).", '".$numeroContrat."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdre->format("Y-m-d")."', '".$resultat."', '".$motifRuptureContrat."', '".$dateReceptionProvisoire->format("Y-m-d")."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEducationEtudes($idInfrastructure = null, $objetContrat = null, $consistanceContrat = null, $entite = null, $idTitulaire = null, $montantContrat = null, $numeroContrat = null, $modePassation = null, $porteAppelOffre = null, $dateContrat = null, $dateOrdreService = null, $resultatPrestation = null, $motifRuptureContrat = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sql = "INSERT into t_ec_12_etudes (id_infrastructure, objet_contrat, consistance_contrat, entite, id_titulaire, montant_contrat, numero_contrat, mode_passation, porte_appel_offre, date_contrat, date_ordre_service, resultat_prestation, motif_rupture_contrat, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$objetContrat."', '".$consistanceContrat."', '".$entite."', '".$idTitulaire."', '".$montantContrat."', '".$numeroContrat."', '".$modePassation."', '".$porteAppelOffre."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdreService->format("Y-m-d")."', '".$resultatPrestation."', '".$motifRuptureContrat."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function getAllInfrastructuresEducation()
    {
        $sql = "SELECT infra.id, infra.nom, infra.indicatif, infra.categorie, infra.localite, infra.commune_terrain, infra.date_information, infra.source_information, infra.mode_acquisition_information, ST_X(infra.geom) AS long, ST_Y(infra.geom) AS lat, infra.numero_sequence, infra.code_produit, infra.code_commune, infra.sous_categorie, infra.district, infra.photo_name1, infra.photo_name2, infra.photo_name3, e.etat, s.fonctionnel, s.raison,d.existence_cantine, d.nombre_enseignant, d.nombre_eleve, f.statut_foncier as status_foncier, f.proprietaire as proprietaire_foncier, f.reference_dossier as reference_dossier_foncier, f.date_information as data_info_foncier, f.source_information as src_info_foncier, f.mode_acquisition_information as mode_acqui_foncier, trav.objet as objet_travaux, trav.consistance_travaux as consistance_travaux, trav.maitre_ouvrage as maitre_ouvrage_travaux, trav.maitre_ouvrage_delegue as maitre_ouvrage_delegue_travaux, trav.maitre_oeuvre as maitre_oeuvre_travaux, trav.id_controle_surveillance as id_controle_surveillance_travaux, trav.mode_passation as mode_passation_travaux, trav.porte_appel_offre as porte_appel_offre_travaux, trav.montant as montant_travaux, trav.numero_contrat as numero_contrat_travaux, trav.date_contrat as date_contrat_travaux, trav.date_ordre_service as date_ordre_service_travaux, trav.id_titulaire as id_titulaire_travaux, trav.resultat_travaux as resultat_travaux_travaux, trav.motif_rupture_contrat as motif_rupture_contrat_travaux, trav.date_reception_provisoire as date_reception_provisoire_travaux, trav.date_reception_definitive as date_reception_definitive_travaux, trav.ingenieur_reception_provisoire as ingenieur_reception_provisoire_travaux, trav.ingenieur_reception_definitive as ingenieur_reception_definitive_travaux, trav.date_information as date_information_travaux, trav.source_information as source_information_travaux, trav.mode_acquisition_information as mode_acquisition_information_travaux, four.objet as objet_fourniture, four.consistance_contrat as consistance_contrat_fourniture, four.materiel_concerne as materiel_concerne_fourniture, four.entite as entite_fourniture, four.id_titulaire as id_titulaire_fourniture, four.mode_passation as mode_passation_fourniture, four.porte_appel_offre as porte_appel_offre_fourniture, four.montant as montant_fourniture, four.numero_contrat as numero_contrat_fourniture, four.date_contrat as date_contrat_fourniture, four.date_ordre_service as date_ordre_service_fourniture, four.resultat_service as resultat_service_fourniture, four.motif_rupture_contrat as motif_rupture_contrat_fourniture, four.date_reception_provisoire as date_reception_provisoire_fourniture, four.date_reception_definitive as date_reception_definitive_fourniture, four.ingenieur_reception_provisoire as ingenieur_reception_provisoire_fourniture, four.ingenieur_reception_definitive as ingenieur_reception_definitive_fourniture, four.date_information as date_information_fourniture, four.source_information as source_information_fourniture, four.mode_acquisition_information as mode_acquisition_information_fourniture, et.objet_contrat as objet_contrat_etude, et.consistance_contrat as objet_contrat_etude, et.entite as entite_etude, et.id_titulaire as id_titulaire_etude, et.montant_contrat as montant_contrat_etude, et.numero_contrat as numero_contrat_etude, et.mode_passation as mode_passation_etude, et.porte_appel_offre as porte_appel_offre_etude, et.date_contrat as date_contrat_etude, et.date_ordre_service as date_ordre_service_etude, et.resultat_prestation as resultat_prestation_etude, et.motif_rupture_contrat as motif_rupture_contrat_etude, et.date_information as date_information_etude, et.source_information as source_information_etude, et.mode_acquisition_information as mode_acquisition_information_etude FROM t_ec_01_infrastructure as infra LEFT JOIN t_ec_04_etat as e ON infra.id = e.id_infrastructure LEFT JOIN t_ec_03_situation as s ON infra.id = s.id_infrastructure LEFT JOIN t_ec_14_donnees_annexes as d ON infra.id = d.id_infrastructure LEFT JOIN t_ec_05_foncier as f ON infra.id = f.id_infrastructure LEFT JOIN t_ec_08_travaux as trav ON infra.id = trav.id_infrastructure LEFT JOIN t_ec_10_fourniture as four ON infra.id = four.id_infrastructure LEFT JOIN t_ec_12_etudes as et ON infra.id = et.id_infrastructure";

        //$sql = "SELECT ST_X(infra.geom) AS X1, ST_Y(infra.geom) AS Y1, ST_X(ST_TRANSFORM(infra.geom,4674)) AS LONG, ST_Y(ST_TRANSFORM(infra.geom,4674)) AS LAT FROM t_ec_01_infrastructure as infra";

        /*$rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addEntityResult(Region::class, "r");

        foreach ($this->getClassMetadata()->fieldMappings as $obj) {
            $rsm->addFieldResult("r", $obj["columnName"], $obj["fieldName"]);
        }

        $stmt = $this->entityManager->createNativeQuery($sql, $rsm);*/
        /* $stmt->setParameter(":current_time", new \DateTime("now"));
        $stmt->setParameter(":status_available", Region::STATUS_AVAILABLE);
        $stmt->setParameter(":status_unknown", Region::STATUS_UNKNOWN);
        $stmt->setParameter(":status_unavailable", Region::STATUS_UNAVAILABLE);*/

        /*$stmt->execute();
        return $stmt->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);*/
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
        /*return $this->entityManager->createQueryBuilder('r')
            ->orderBy('r.nom', 'ASC')
            ->getQuery()
            ->getResult()
            ;*/
    }

    /*public function getAllCommunesByRegion($region)
    {
        $sql = "SELECT * FROM commune as c where region_id = " . $region . " order by c.nom";


        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function getAllCommunesByRegionName($region)
    {
        $sql = "SELECT * FROM limite_communes_mada as c where c.nom_region = '" . $region . "' order by c.nom_commun ASC";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function getCommuneById($commune)
    {
        $sql = "SELECT c.nom_commun as nom_commune, r.nomreg as nom_region, r.gid as region_id, c.nom_distri as nom_district  FROM limite_communes_mada as c";
        $sql .= " LEFT JOIN mada_region as r ON c.nom_region = r.nomreg";
        $sql .= " where c.gid = " . $commune . "";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function searchReglementSac($tableName = "", $xV = "", $yV = "")
    {
        $sql = "SELECT  cos_sac, voca_env FROM " . $tableName . "";
        $sql .= " WHERE  st_intersects (geom, ST_GeometryFromText('POINT(" . $xV . " " . $yV . ")', 29702))";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function searchReglementPude($tableName = "", $xV = "", $yV = "")
    {
        $sql = "SELECT  zoning, reglements FROM " . $tableName . "";
        $sql .= " WHERE  st_intersects (geom, ST_GeometryFromText('POINT(" . $xV . " " . $yV . ")', 29702))";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function getParcel($tableName = "", $xV = "", $yV = "")
    {
        $sql = "SELECT  id_plof_bypass FROM " . $tableName . "";
        $sql .= " WHERE  st_intersects (geom, ST_GeometryFromText('POINT(" . $xV . " " . $yV . ")', 29702))";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function getCouchePlof($tableName = null)
    {
        $sql = "SELECT ST_ASGeoJSON(geom) AS geom FROM " . $tableName . "";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function calculDistance($plof, $tableName, $EducationTable)
    {
        $sql = "SELECT rt.linewt, st_distance(rt.geom,p.geom) AS al FROM " . $tableName . " as p, " . $EducationTable . " as rt";
        $sql .= " WHERE  p.id_plof_bypass = " . $plof . " ORDER BY al ASC LIMIT 1";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function searchReglementPudi($tableName = "", $xV = "", $yV = "")
    {
        $sql = "SELECT  s_categori, reglement_pudi_os FROM " . $tableName . "";
        $sql .= " WHERE  st_intersects (geom, ST_GeometryFromText('POINT(" . $xV . " " . $yV . ")', 29702))";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function getEducationPrimaire($id_plof = null, $xV = "", $yV = "")
    {
        $sql = "SELECT a.linewt, st_distance(a.geom,b.geom) AS al, st_x(st_centroid(st_shortestline(a.geom, b.geom))) AS x_lalana, st_y(st_centroid(st_shortestline(a.geom, b.geom))) AS y_lalana FROM plof_avaradrano b, voie_primaire_shp a ";
        $sql .= "WHERE b.id_plof_bypass = " . $id_plof . "";
        $sql .= " ORDER BY al ASC LIMIT 1;";


        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // /**
    //  * @return Commune[] Returns an array of Commune objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commune
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}