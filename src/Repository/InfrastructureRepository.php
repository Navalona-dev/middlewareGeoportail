<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class InfrastructureRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager("middleware");
    }

    public function addEducation($nom = null, $indicatif = null, $categorie = null, $localite = null, $sourceInformation = null, $modeAcquisitionInformation = null, $communeTerrain = null, $numeroSequence = null, $codeProduit = null, $codeCommune = null, $latitude = null, $longitude = null )
    {
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ec_01_infrastructure (nom, indicatif, categorie, localite, commune_terrain, date_information, source_Information, mode_acquisition_information, geom,  numero_sequence, code_produit, code_commune ) VALUES ('".$nom."', '".$indicatif."', '".$categorie."', '".$localite."', '".$communeTerrain."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', ST_GeomFromText('POINT(" . $longitude . " " . $latitude . ")', 4326), '".$numeroSequence."', ".$codeProduit.", ".$codeCommune.")";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        
        return $query->execute();
    }
    
    public function getAllDomainesInfrastructure()
    {
        $sql = "SELECT * FROM code_infra";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllNiveauInfrastructureByDomaineNiveau3($domaine = null)
    {
        $sql = "select * from niveau_3 where niveau_3.\"indicatif infrastructure\" ILIKE '" . $domaine . "%'";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllIndicatifNiveau3()
    {
        $sql = "select * from niveau_3";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllIndicatifNiveau2()
    {
        $sql = "select * from niveau_2";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllSourceInfo()
    {
        $sql = "select * from x_source_informations";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    
    public function getAllNiveauInfrastructureByDomaine($domaine = null)
    {
        $sql = "select * from (select * from niveau_2  UNION select * from niveau_3) as niveau where niveau.code_2 ILIKE '" . $domaine . "%'";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllPrestataireInfo()
    {
        $sql = "select id, nom from x_prestataires";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllCategorieInfo()
    {
        $sql = "select infrastucture, categorie from x_categorie";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getOuiNonInfo()
    {
        $sql = "select reponse from y_oui_non";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getMotifNonFonctionnelInfo()
    {
        $sql = "select motif from x_raison_non_fonctionnel";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getModeAcquisitionInfo()
    {
        $sql = "select mode from x_mode_acquisition_information";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getModePassationMarcheInfo()
    {
        $sql = "select designation from x_mode_passation_marche";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllMaitreOuvrageInfo()
    {
        $sql = "select nom from x_maitre_ouvrage";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getMotifRuptureContratInfo($type = null)
    {
        $sql = "select motif from x_motif_rupture_contrat_".$type;

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getConsistanceTravauxInfo($type = null)
    {
        $sql = "select consistance from x_consistance_contrat_".$type;

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }

    public function getAllIngenieursInfo()
    {
        $sql = "select nom_prenoms from x_ingenieurs";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
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