<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class RouteRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager("middleware");
    }

    public function addInfrastructureRoute($categorie = null, $localite = null, $sourceInformation = null, $modeAcquisitionInformation = null, $communeTerrain = null, $pkDebut = null, $rattache = null, $gestionnaire = null, $modeGestion = null, $pkFin = null, $largeurHausse = null, $largeurAccotement = null,$structure = null, $region = null, $district = null, $longitude = null, $latitude = null, $photo1 = null, $photo2 = null, $photo3 = null )
    {
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_01_infrastructure (pk_debut, rattache, categorie, localite,  commune_terrain, gestionnaire, mode_gestion, date_information, source_Information, mode_acquisition_infromation, pk_fin, \"Largeur_chaussée\", \"Largeur_accotements\", Structure, region, district, geom, photo1, photo2, photo3) VALUES ('".$pkDebut."', '".$rattache."', '".$categorie."', '".$localite."', '".$communeTerrain."', '".$gestionnaire."', '".$modeGestion."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$pkFin."', '".$largeurHausse."', '".$largeurAccotement."', '".$structure."', '".$region."', '".$district."', ST_GeomFromText('POINT(" . $longitude . " " . $latitude . ")', 4326), '".$photo1."', '".$photo2."', '".$photo3."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }
    
    public function getAllInfrastructuresRoute()
    {
        $sql = "SELECT id, nom, indicatif, categorie, localite, commune_terrain, date_information, source_information, mode_acquisition_information, ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat, numero_sequence, code_produit, code_commune  FROM t_ro_01_infrastructure as infraroute";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getAllInfrastructuresBaseRoute()
    {
        $sql = "SELECT ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat, infrabaseroute.nom as rattache  FROM y_liste_route as infrabaseroute";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function addInfrastructureBaseRoute($coordonnées = null, $nom = null )
    {
        $sql = "INSERT into y_liste_route (geom, nom) VALUES (ST_GeomFromText('MULTILINESTRING((".$coordonnées."))'), '".$nom."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);

        return $query->execute();
    }
    
    public function addInfrastructureRouteEtat($idInfrastructure = null, $etat = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_03_etat (id_infrastructure, etat, date_information, source_Information, mode_acquisition_information) VALUES (".$idInfrastructure.", '".$etat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteSituation($idInfrastructure = null, $fonctionnel = null, $raison, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_02_situation (id_infrastructure, fonctionnel, raison, date_information, source_Information, mode_acquisition_information) VALUES (".$idInfrastructure.", '".$fonctionnel."', '".$raison."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteSurface($idInfrastructure = null, $revetement = null, $revetueNidDePoule = null, $revetueArrachement = null, $revetueRessuage = null, $revetueFissureLogitudinaleDeJoint = null, $nonRevetueTraverse = null, $nonRevetueBourbier = null, $nonRevetueTeteDeChat = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_04_surface (id_infrastructure, revetement, revetue_nid_de_poule, revetue_arrachement, revetue_ressuage, revetue_fissure_logitudinale_de_joint, non_revetue_traverse, non_revetue_bourbier, non_revetue_tete_de_chat, date_information, source_Information, mode_acquisition_infromation) VALUES (".$idInfrastructure.", '".$revetement."', '".$revetueNidDePoule."', '".$revetueArrachement."', '".$revetueRessuage."', '".$revetueFissureLogitudinaleDeJoint."', '".$nonRevetueTraverse."', '".$nonRevetueBourbier."', '".$nonRevetueTeteDeChat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $revetueDefomation = null, $revetueFissuration = null, $revetueFaiencage = null, $nonRevetueNidsDpoule = null, $nonRevetueDeformation = null,  $nonRevetueToleOndule = null,$nonRevetueRavines = null,  $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_05_structure (id_infrastructure, revetue_defomation, revetue_fissuration, revetue_faiencage, non_revetue_nids_de_poule, non_revetue_deformation, non_revetue_tole_ondule, non_revetue_ravines, date_information, source_Information,  mode_acquisition_information) VALUES (".$idInfrastructure.", '".$revetueDefomation."', '".$revetueFissuration."', '".$revetueFaiencage."', '".$nonRevetueNidsDpoule."', '".$nonRevetueDeformation."', '".$nonRevetueToleOndule."', '".$nonRevetueRavines."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteCollecte($idInfrastructure = null,  $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_06_collectees (id_infrastructure, date_information, source_Information, mode_acquisition_information) VALUES (".$idInfrastructure.", '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteAccotement($idCollecteDonnees = null, $cote = null, $revetueDegradationSurface = null, $revetueDentelleRive = null,  $revetueDenivellationEntreChausséeAccotement = null,$revetueDestructionAffouillementAccotement = null,  $nonRevetueDeformationProfil = null, $revetu = null)
    {   
        $sql = "INSERT into t_ro_07_accotement (id_collecte_donnees, cote, revetue_degradation_de_la_surface, revetue_dentelle_de_rive, revetue_denivellation_entre_chaussée_et_accotement, revetue_destruction_par_affouillement_de_accotement, non_revetue_deformation_du_profil, revetu) VALUES (".$idCollecteDonnees.", '".$cote."', '".$revetueDegradationSurface."', '".$revetueDentelleRive."', '".$revetueDenivellationEntreChausséeAccotement."', '".$revetueDestructionAffouillementAccotement."', '".$nonRevetueDeformationProfil."', '".$revetu."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteFosse($idCollecteDonnees = null, $cote = null, $revetueDegradationFosse = null,  $revetueSectionBouche = null,$nonRevetueProfil = null,  $nonRevetueEncombrement = null, $revetu = null)
    {   
        $sql = "INSERT into t_ro_08_fosse (cote, revetue_degradation_du_fosse, revetue_section_bouche, non_revetue_profil, non_revetue_encombrement, id_collecte_donnees,  revetu) VALUES ('".$cote."', '".$revetueDegradationFosse."', '".$revetueSectionBouche."', '".$nonRevetueProfil."', '".$nonRevetueEncombrement."', ".$idCollecteDonnees.", '".$revetu."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
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

    public function calculDistance($plof, $tableName, $routeTable)
    {
        $sql = "SELECT rt.linewt, st_distance(rt.geom,p.geom) AS al FROM " . $tableName . " as p, " . $routeTable . " as rt";
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

    public function getRoutePrimaire($id_plof = null, $xV = "", $yV = "")
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