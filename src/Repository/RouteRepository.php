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

    public function addInfrastructureRoute($categorie = null, $localite = null, $sourceInformation = null, $modeAcquisitionInformation = null, $communeTerrain = null, $latitude = null, $longitude = null, $pkDebut = null, $section = null, $rattache = null, $gestionnaire = null, $modeGestion = null, $numero = null, $pkFin = null, $lineaire = null, $largeurHausse = null, $largeurAccotement = null, $structure = null, $region = null, $district = null, $gps = null )
    {
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_01_infrastructure (pk_debut, rattache, geom, \"section\", categorie, localite,  commune_terrain, gestionnaire, mode_gestion, date_information, source_Information, mode_acquisition_infromation, \"Numero\", pk_fin, lineaire, \"Largeur de la chaussée\", \"Largeur des accotements\", \"Structure\", region, district, gps) VALUES ('".$pkDebut."', '".$rattache."', ST_GeomFromText('POINT(" . $longitude . " " . $latitude . ")', 4326), '".$section."', '".$categorie."', '".$localite."', '".$communeTerrain."', '".$gestionnaire."', '".$modeGestion."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$numero."', '".$pkFin."', '".$lineaire."', '".$largeurHausse."', '".$largeurAccotement."', '".$structure."', '".$region."', '".$district."', '".$gps."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        
        return $query->execute();
    }
    
    public function getAllInfrastructuresRoute()
    {
        $sql = "SELECT id, nom, indicatif, categorie, localite, commune_terrain, date_information, source_information, mode_acquisition_information, ST_X(infra.geom) AS long, ST_Y(infra.geom) AS lat, numero_sequence, code_produit, code_commune  FROM t_ec_01_infrastructure as infra";

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