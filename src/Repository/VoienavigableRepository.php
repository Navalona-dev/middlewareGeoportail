<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class VoienavigableRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager("middleware");
    }

    public function addInfrastructure($nom = null, $categorie = null, $indicatif = null, $nomVoieNavigableRattache = null, $localiteDepart = null, $localite = null, $communeTerrain = null, $sourceInformation = null, $modeAcquisitionInformation = null, $coordonnees, $district = null, $localiteArrivee = null, $chargeMaximum = null, $region = null, $photo1 = null, $photo2 = null, $photo3 = null, $photo_name1 = null, $photo_name2 = null, $photo_name3 = null, $moisOuverture = null, $moisFermeture = null )
    {
        $dateInfo = new \DateTime();
        $localite = pg_escape_string($localite);
        $communeTerrain = pg_escape_string($communeTerrain);
        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $region = pg_escape_string($region);
        $nom = pg_escape_string($nom);
        $sql = "INSERT into t_vn_01_infrastructure (geom, nom, indicatif, categorie, nom_de_la_voie_navigable_a_qui_il_est_rattache,localite_depart, localite_arrivee, mois_ouverture, mois_fermeture, commune_sur_terrain, localite, date_information, source_information, mode_acquisition_infromation, photo_name1, photo_name2, photo_name3, district, region, photo1, photo2, photo3) VALUES (ST_GeomFromText('LINESTRING(".$coordonnees.")'), '".$nom."', '".$indicatif."', '".$categorie."', '".$nomVoieNavigableRattache."', '".$localiteDepart."', '".$localiteArrivee."', '".$moisOuverture."', '".$moisFermeture."', '".$communeTerrain."', '".$localite."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$photo_name1."', '".$photo_name2."', '".$photo_name3."', '".$district."', '".$region."', '".$photo1."', '".$photo2."', '".$photo3."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }
    
    public function addInfrastructurePhoto($idInfra = null, $setUpdate )
    {
        $sql = "UPDATE t_vn_01_infrastructure SET ".$setUpdate." where id = ".$idInfra."";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->executeQuery();
     
        return $idInfra;
    }

    public function getAllCategorieInfra()
    {
        $sql = "SELECT categorie.categorie as categorie FROM x_categorie as categorie  where categorie.infrastucture = 'Bac'";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getPhotoInfraInfo($infraId)
    {
        $sql = "SELECT infra.id as infra_id, infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3 FROM t_vn_01_infrastructure as infra  where infra.id = ".intval($infraId)."";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getAllInfrastructures()
    {
        $sql = 'SELECT infra.id as infra_id, infra.categorie, infra.precision_categorie, infra.indicatif, infra.nom, infra.localite, infra.mois_ouverture, infra.mois_fermeture, infra.commune_sur_terrain, infra.nom_de_la_voie_navigable_a_qui_il_est_rattache, infra.point_kilometrique_de_son_implantation, infra.date_information, infra.source_information as source_information, infra.district, infra.charge_maximum, infra.region,  ST_X(infra.geom) AS long, ST_Y(infra.geom) AS lat, infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3, situation.etat as etat, situation.fonctionnel as situation_fonctionnel, situation.raison as motif_etat, situation.date_information as situation_data_info, situation.source_information as situation_src_info, situation.mode_acquisition_information as situation_mode_aquis_info, dc.id as data__id, dc.id_infrastructure as data__id_infrastructure, dc.duree_theorique_de_la_traversee as data__duree_theorique_de_la_traversee, dc.duree_reelle_de_la_traversee as data__duree_reelle_de_la_traversee, dc.etat_de_la_coque_du_bac as data__etat_de_la_coque_du_bac, dc.etat_du_moteur_du_bac as data__etat_du_moteur_du_bac, dc.date_information as data__date_information, dc.source_information as data__source_information, dc.mode_acquisition_information as data__mode_acquisition_information, trav.bailleur as bailleur_travaux, trav.objet as travaux_objet, trav.consistance_travaux as travaux_consistance_travaux, trav.maitre_ouvrage as travaux_maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux_maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux_maitre_oeuvre, trav.id_controle_surveillance as travaux_id_controle_surveillance, trav.mode_passation as travaux_mode_passation, trav.porte_appel_offre as travaux_porte_appel_offre, trav.montant as travaux_montant, trav.numero_contrat as travaux_numero_contrat, trav.date_contrat as travaux_date_contrat, trav.date_ordre_service as travaux_date_ordre_service, trav.id_titulaire as travaux_id_titulaire, trav.resultat_travaux as travaux_resultat_travaux, trav.motif_rupture_contrat as travaux_motif_rupture_contrat, trav.date_reception_provisoire as travaux_date_reception_provisoire, trav.date_reception_definitive as travaux_date_reception_definitive, trav.ingenieur_reception_provisoire as travaux_ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux_ingenieur_reception_definitive, trav.date_information as travaux_date_information, trav.source_information as travaux_source_information, trav.mode_acquisition_information as travaux_mode_acquisition_information, etude.consistance_contrat as consistance_contrat_etude, etude.bailleur as bailleur_etude, etude.objet_contrat as etude_objet_contrat, etude.entite as etude_entite, etude.id_titulaire as etude_id_titulaire, etude.montant_contrat as etude_montant_contrat, etude.numero_contrat as etude_numero_contrat, etude.mode_passation as etude_mode_passation, etude.porte_appel_offre as etude_porte_appel_offre, etude.date_contrat as etude_date_contrat, etude.date_ordre_service as etude_date_ordre_service, etude.resultat_prestation as etude_resultat_prestation, etude.motif_rupture_contrat as etude_motif_rupture_contrat, etude.date_information as etude_date_information, etude.source_information as etude_source_information, etude.mode_acquisition_information as etude_mode_acquisition_information  FROM t_vn_01_infrastructure as infra LEFT JOIN t_vn_02_situation as situation ON infra.id = situation.id_infrastructure  LEFT JOIN t_vn_04_donnees_collectees as dc ON infra.id = dc.id_infrastructure  LEFT JOIN t_vn_05_travaux as trav ON infra.id = trav.id_infrastructure LEFT JOIN t_vn_07_etudes as etude ON infra.id = etude.id_infrastructure';

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getAllInfrastructuresMinifie()
    {
        $sql = 'SELECT infra.id as infra_id, infra.nom as nom, infra.categorie, infra.nom_de_la_voie_navigable_a_qui_il_est_rattache, infra.localite, infra.mois_ouverture, infra.mois_fermeture, infra.commune_sur_terrain, infra.localite_depart as localite_depart, infra.localite_arrivee, infra.date_information, infra.source_information as source_information, infra.mode_acquisition_infromation as mode_acquisition_infromation, infra.district, ST_AsText(infra.geom) as coordonnees, infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3  FROM t_vn_01_infrastructure as infra';

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getInfoyRouteInfoMinifie($id)
    {
        $sql = 'select route.gid as id, route.nom as nom, route.num as numero from y_liste_route as route where route.gid = '.$id.'';

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }
    
    public function getAllyRouteInfoMinifie()
    {
        $sql = 'select MAX(route.gid) as id, route.nom as nom, MAX(route.num) as numero from y_liste_route as route GROUP BY route.nom';

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }
    
    public function getOneInfraInfo($infraId)
    {
        /*$sql = "SELECT infra.id as infra_id, CASE 
        WHEN infra.categorie = 'Autre à préciser' THEN infra.precision_categorie 
        ELSE infra.categorie 
        END AS categorie, infra.precision_categorie, infra.indicatif, infra.nom, infra.localite, infra.mois_ouverture, infra.mois_fermeture, infra.commune_sur_terrain, infra.nom_de_la_route_a_qui_il_est_rattache as nom_de_la_route_a_qui_il_est_rattache, infra.point_kilometrique_de_son_implantation, infra.date_information, infra.source_information as source_information, infra.mode_acquisition_information as mode_acquisition_information, infra.district, infra.charge_maximum, infra.region,  ST_X(infra.geom) AS long, ST_Y(infra.geom) AS lat, infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3, situation.id as situation__id, situation.id_infrastructure as situation__id_infrastructure, situation.fonctionnel as situation__fonctionnel, situation.raison as situation__raison, situation.date_information as situation__date_information, situation.source_information as situation__source_information, situation.mode_acquisition_information as situation__mode_acquisition_information, situation.etat as situation__etat, situation.raison as situation__raison, dc.id as data__id, dc.id_infrastructure as data__id_infrastructure, CASE 
        WHEN dc.duree_theorique_de_la_traversee = '0' THEN NULL 
        ELSE dc.duree_theorique_de_la_traversee 
        END AS duree_theorique_de_la_traversee, CASE WHEN dc.duree_reelle_de_la_traversee = '0' THEN NULL 
        ELSE dc.duree_reelle_de_la_traversee 
        END AS duree_reelle_de_la_traversee, dc.etat_de_la_coque_du_bac as data__etat_de_la_coque_du_bac, dc.etat_du_moteur_du_bac as data__etat_du_moteur_du_bac, dc.date_information as data__date_information, dc.source_information as data__source_information, dc.mode_acquisition_information as data__mode_acquisition_information, trav.id as travaux__id, trav.id_infrastructure as travaux__id_infrastructure, trav.objet as travaux__objet, trav.consistance_travaux as travaux__consistance_travaux, trav.maitre_ouvrage as travaux__maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux__maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux__maitre_oeuvre, trav.id_controle_surveillance as travaux__id_controle_surveillance, trav.mode_passation as travaux__mode_passation, trav.porte_appel_offre as travaux__porte_appel_offre, trav.montant as travaux__montant, trav.numero_contrat as travaux__numero_contrat, trav.date_contrat as travaux__date_contrat, trav.date_ordre_service as travaux__date_ordre_service, trav.id_titulaire as travaux__id_titulaire, trav.resultat_travaux as travaux__resultat_travaux, trav.motif_rupture_contrat as travaux__motif_rupture_contrat, trav.date_reception_provisoire as travaux__date_reception_provisoire, trav.date_reception_definitive as travaux__date_reception_definitive, trav.ingenieur_reception_provisoire as travaux__ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux__ingenieur_reception_definitive, trav.date_information as travaux__date_information, trav.source_information as travaux__source_information, trav.mode_acquisition_information as travaux__mode_acquisition_information, trav.bailleur as travaux__bailleur, etude.id as etude__id, etude.id_infrastructure as etude__id_infrastructure, etude.objet_contrat as etude__objet_contrat, etude.consistance_contrat as etude__consistance_contrat, etude.entite as etude__entite, etude.id_titulaire as etude__id_titulaire, etude.montant_contrat as etude__montant_contrat, etude.numero_contrat as etude__numero_contrat, etude.mode_passation as etude__mode_passation, etude.porte_appel_offre as etude__porte_appel_offre, etude.date_contrat as etude__date_contrat, etude.date_ordre_service as etude__date_ordre_service, etude.resultat_prestation as etude__resultat_prestation, etude.motif_rupture_contrat as etude__motif_rupture_contrat, etude.date_information as etude_date_information, etude.source_information as etude__source_information, etude.mode_acquisition_information as etude__mode_acquisition_information, etude.bailleur as etude__bailleur  FROM t_vn_01_infrastructure as infra LEFT JOIN t_vn_02_situation as situation ON infra.id = situation.id_infrastructure  LEFT JOIN t_vn_04_donnees_collectees as dc ON infra.id = dc.id_infrastructure  LEFT JOIN t_vn_05_travaux as trav ON infra.id = trav.id_infrastructure LEFT JOIN t_vn_07_etudes as etude ON infra.id = etude.id_infrastructure where infra.id = ".$infraId."";
        */
        $sql = "SELECT infra.id as infra_id, ST_AsText(infra.geom) as coordonnees, infra.nom, infra.indicatif, infra.categorie, infra.nom_de_la_voie_navigable_a_qui_il_est_rattache, infra.localite_depart, infra.localite_arrivee, infra.mois_ouverture, infra.mois_fermeture, infra.commune_sur_terrain, infra.localite, infra.date_information, infra.source_information as source_information, infra.mode_acquisition_infromation as mode_acquisition_infromation, infra.photo_name1, infra.photo_name2, infra.photo_name3, infra.district, infra.region, infra.photo1, infra.photo2, infra.photo3, situation.id as situation__id, situation.id_infrastructure as situation__id_infrastructure, situation.fonctionnel as situation__fonctionnel, situation.raison as situation__raison, situation.date_information as situation__date_information, situation.source_information as situation__source_information, situation.mode_acquisition_information as situation__mode_acquisition_information, situation.etat as situation__etat, situation.raison as situation__raison, dc.id as data__id, dc.id_infrastructure as data__id_infrastructure, dc.etat_ensablement_du_canal as data__etat_ensablement_du_canal, dc.etat_erosion_de_la_berge as data__etat_erosion_de_la_berge, dc.existence_du_chenal as data__existence_du_chenal, dc.etat_de_l_ensablement_du_chenal as data__etat_de_l_ensablement_du_chenal, dc.existence_balise as data__existence_balise, dc.etat_global_des_balises as data__etat_global_des_balises, dc.existence_digue as data__existence_digue, dc.fuite_sur_la_digue as data__fuite_sur_la_digue, dc.existence_des_trous_sur_la_digue as data__existence_des_trous_sur_la_digue, dc.existence_erosion_le_long_de_la_recharge_aval as data__existence_erosion_le_long_de_la_recharge_aval, dc.existence_ouvrage_d_accostage as data__existence_ouvrage_d_accostage, dc.type_ouvrage_d_accostage as data__type_ouvrage_d_accostage, dc.existence_de_fissure_pour_ouvrage_en_beton as data__existence_de_fissure_pour_ouvrage_en_beton, dc.existence_de_ferraillage_visible_pour_ouvrage_en_beton as data__existence_de_ferraillage_visible_pour_ouvrage_en_beton, dc.existence_de_fissure_pour_ouvrage_en_maconnerie as data__existence_de_fissure_pour_ouvrage_en_maconnerie, dc.existence_de_rouille_pour_ouvrage_en_fer as data__existence_de_rouille_pour_ouvrage_en_fer, dc.date_information as data__date_information, dc.source_information as data__source_information, dc.mode_acquisition_information as data__mode_acquisition_information, trav.id as travaux__id, trav.id_infrastructure as travaux__id_infrastructure, trav.objet as travaux__objet, trav.consistance_travaux as travaux__consistance_travaux, trav.maitre_ouvrage as travaux__maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux__maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux__maitre_oeuvre, trav.id_controle_surveillance as travaux__id_controle_surveillance, trav.mode_passation as travaux__mode_passation, trav.porte_appel_offre as travaux__porte_appel_offre, trav.montant as travaux__montant, trav.numero_contrat as travaux__numero_contrat, trav.date_contrat as travaux__date_contrat, trav.date_ordre_service as travaux__date_ordre_service, trav.id_titulaire as travaux__id_titulaire, trav.resultat_travaux as travaux__resultat_travaux, trav.motif_rupture_contrat as travaux__motif_rupture_contrat, trav.date_reception_provisoire as travaux__date_reception_provisoire, trav.date_reception_definitive as travaux__date_reception_definitive, trav.ingenieur_reception_provisoire as travaux__ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux__ingenieur_reception_definitive, trav.date_information as travaux__date_information, trav.source_information as travaux__source_information, trav.mode_acquisition_information as travaux__mode_acquisition_information, trav.bailleur as travaux__bailleur, etude.id as etude__id, etude.id_infrastructure as etude__id_infrastructure, etude.objet_contrat as etude__objet_contrat, etude.consistance_contrat as etude__consistance_contrat, etude.entite as etude__entite, etude.id_titulaire as etude__id_titulaire, etude.montant_contrat as etude__montant_contrat, etude.numero_contrat as etude__numero_contrat, etude.mode_passation as etude__mode_passation, etude.porte_appel_offre as etude__porte_appel_offre, etude.date_contrat as etude__date_contrat, etude.date_ordre_service as etude__date_ordre_service, etude.resultat_prestation as etude__resultat_prestation, etude.motif_rupture_contrat as etude__motif_rupture_contrat, etude.date_information as etude_date_information, etude.source_information as etude__source_information, etude.mode_acquisition_information as etude__mode_acquisition_information, etude.bailleur as etude__bailleur  FROM t_vn_01_infrastructure as infra LEFT JOIN t_vn_02_situation as situation ON infra.id = situation.id_infrastructure  LEFT JOIN t_vn_04_donnees_collectees as dc ON infra.id = dc.id_infrastructure  LEFT JOIN t_vn_05_travaux as trav ON infra.id = trav.id_infrastructure LEFT JOIN t_vn_07_etudes as etude ON infra.id = etude.id_infrastructure where infra.id = ".$infraId."";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

   /* public function getAllInfrastructuresBaseRoute()
    {
        $sql = "SELECT ST_X(infra.geom) AS long, ST_Y(infra.geom) AS lat, infrabaseroute.nom as rattache  FROM y_liste_route as infrabaseroute";
        
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
        $sql = "INSERT into t_ro_03_etat (id_infrastructure, etat, date_information, source_Information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$etat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }*/

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        if (null != $idInfrastructure && $type != null) {

            $table = 't_vn_01_infrastructure';
            $colonne = "id";
            $selectedcolonne = "id";
            switch ($type) {
                case 'situation':
                    $table = "t_vn_02_situation";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                /*case 'surface':
                    $table = "t_ro_04_surface";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;*/
                case 'data':
                    $table = "t_vn_04_donnees_collectees";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                /*case 'etat':
                    $table = "t_dar_03_etat";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                /*case 'accotement':
                    $table = "t_ro_07_accotement";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'fosse':
                    $table = "t_ro_08_fosse";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'foncier':
                    $table = "t_ro_13_foncier";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;*/
                case 'travaux':
                    $table = "t_vn_05_travaux";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                /*case 'fourniture':
                    $table = "t_ro_14_fourniture";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;*/
                case 'etude':
                    $table = "t_vn_07_etudes";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                default:
                    $table = 't_vn_01_infrastructure';
                    $colonne = "id";
                    $selectedcolonne = "id";
                    break;
            }

            $sql = "SELECT $selectedcolonne FROM $table where $colonne = ".intval($idInfrastructure)."";
            $conn = $this->entityManager->getConnection();
            $query = $conn->prepare($sql);

            //$query->execute();

            $result = $query->executeQuery();

            $row = $result->fetchAllAssociative();

            if (null != $row && count($row) > 0 && null != $row[0] && null != $row[0][$selectedcolonne]) {
                $this->deleteByIdInfrastructure($row[0][$selectedcolonne], $table, $colonne);
            }
        }
    }

    public function deleteByIdInfrastructure($id = null, $table = null, $colonne = null)
    {
        if (null != $table && null != $id) {
            $sql = "DELETE FROM $table where $colonne = ".$id."";
            $conn = $this->entityManager->getConnection();
            $query = $conn->prepare($sql);
            $query->execute();
        }  
    }

    public function updateInfrastructure($idInfra = null, $updateColonneInfra = null)
    {
        $dateInfo = new \DateTime();
        $sql = "UPDATE t_vn_01_infrastructure SET ".$updateColonneInfra." where id = ".$idInfra."";
       
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->executeQuery();

        return $idInfra;
    }

    public function addInfoInTableByInfrastructure($table, $colonnes, $values)
    {   
        $sql = "INSERT into ".$table." (".$colonnes.") VALUES (".$values.")";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }
    
    public function updateInfrastructureTables($table = null, $idRow = null, $updateColonne = null)
    {
        $dateInfo = new \DateTime();
        $sql = "UPDATE ".$table." SET ".$updateColonne." where id = ".$idRow."";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->executeQuery();

        return $idRow;
    }

    public function addInfrastructureSituation($idInfrastructure = null, $fonctionnel = null, $motif = null, $sourceInformation = null, $modeAcquisitionInformation = null, $etat = null, $raisonPrecision = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_vn_02_situation (id_infrastructure, fonctionnel, raison, date_information, source_information, mode_acquisition_information, etat) VALUES (".intval($idInfrastructure).", '".$fonctionnel."', '".$motif."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."', '".$etat."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureDonneCollecte($idInfrastructure = null, $etatEnsablementCanal = null, $existenceChenal = null, $etatEnsablementChenal = null, $existenceBalise = null, $etatGlobalBalises = null, $existenceDigue = null, $fuiteDigue = null, $existenceTrousDigue = null, $existenceErosionLongRechargeAval = null, $existenceOuvrageAccostage = null, $typeOuvrageAccostage = null, $existenceFissureOuvrageBeton = null, $existenceFerraillageVisiblePourOuvrageBeton = null, $existenceFissurePourOuvrageMaconnerie = null, $existencePouillePourOuvrageFer = null,  $sourceInformation = null,  $modeAcquisitionInformation = null)
    {   
        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_vn_04_donnees_collectees (id_infrastructure, etat_ensablement_du_canal, etat_erosion_de_la_berge, existence_du_chenal, etat_de_l_ensablement_du_chenal, existence_balise, etat_global_des_balises, existence_digue, fuite_sur_la_digue, existence_des_trous_sur_la_digue, existence_erosion_le_long_de_la_recharge_aval, existence_ouvrage_d_accostage, type_ouvrage_d_accostage, existence_de_fissure_pour_ouvrage_en_beton, existence_de_ferraillage_visible_pour_ouvrage_en_beton, existence_de_fissure_pour_ouvrage_en_maconnerie, existence_de_rouille_pour_ouvrage_en_fer, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$etatEnsablementCanal."', '".$existenceChenal."', '".$etatEnsablementChenal."', '".$existenceBalise."', '".$etatGlobalBalises."', '".$existenceDigue."', '".$etatGlobalBalises."', '".$fuiteDigue."', '".$existenceTrousDigue."', '".$existenceErosionLongRechargeAval."', '".$existenceOuvrageAccostage."', '".$typeOuvrageAccostage."', '".$existenceFissureOuvrageBeton."', '".$existenceFerraillageVisiblePourOuvrageBeton."', '".$existenceFissurePourOuvrageMaconnerie."', '".$existencePouillePourOuvrageFer."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    /*public function addInfrastructureRouteStructure($idInfrastructure = null, $revetueDefomation = null, $revetueFissuration = null, $revetueFaiencage = null, $nonRevetueNidsDpoule = null, $nonRevetueDeformation = null,  $nonRevetueToleOndule = null,$nonRevetueRavines = null,  $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_05_structure (id_infrastructure, revetue_defomation, revetue_fissuration, revetue_faiencage, non_revetue_nids_de_poule, non_revetue_deformation, non_revetue_tole_ondule, non_revetue_ravines, date_information, source_Information,  mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$revetueDefomation."', '".$revetueFissuration."', '".$revetueFaiencage."', '".$nonRevetueNidsDpoule."', '".$nonRevetueDeformation."', '".$nonRevetueToleOndule."', '".$nonRevetueRavines."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteAccotement($idInfrastructure = null, $cote = null, $revetueDegradationSurface = null, $revetueDentelleRive = null,  $revetueDenivellationEntreChausséeAccotement = null,$revetueDestructionAffouillementAccotement = null,  $nonRevetueDeformationProfil = null, $revetu = null, $accotementTypeRevetementAccotement = null, $accotementPrecisionTypeAccotement =null, $dateInfo = null,  $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);

        $sql = "INSERT into t_ro_07_accotement (id_infrastructure, cote, revetue_degradation_de_la_surface, revetue_dentelle_de_rive, revetue_denivellation_entre_chaussée_et_accotement, revetue_destruction_par_affouillement_de_accotement, non_revetue_deformation_du_profil, revetu, \"type\", precision_type, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$cote."', '".$revetueDegradationSurface."', '".$revetueDentelleRive."', '".$revetueDenivellationEntreChausséeAccotement."', '".$revetueDestructionAffouillementAccotement."', '".$nonRevetueDeformationProfil."', '".$revetu."', '".$accotementTypeRevetementAccotement."', '".$accotementPrecisionTypeAccotement."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteFosse($idInfrastructure = null, $cote = null, $revetueDegradationFosse = null,  $revetueSectionBouche = null,$nonRevetueProfil = null,  $nonRevetueEncombrement = null, $revetu = null, $dateInfo = null,  $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);

        $sql = "INSERT into t_ro_08_fosse (cote, revetue_degradation_du_fosse, revetue_section_bouche, non_revetue_profil, non_revetue_encombrement, id_infrastructure,  revetu, date_information, source_information, mode_acquisition_information) VALUES ('".$cote."', '".$revetueDegradationFosse."', '".$revetueSectionBouche."', '".$nonRevetueProfil."', '".$nonRevetueEncombrement."', ".intval($idInfrastructure).", '".$revetu."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteFoncier($statut = null, $numeroReference = null, $nomProprietaire = null, $idInfrastructure = null)
    {   
        $sql = "INSERT into t_ro_13_foncier (\"Statut\", numero_de_reference, nom_proprietaire, id_infrastructure) VALUES ('".$statut."', '".$numeroReference."', '".$nomProprietaire."', ".intval($idInfrastructure).")";
       // dd($sql, $statut);
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }*/

    public function addInfrastructureTravaux($idInfrastructure = null, $objet = null, $consistanceTravaux = null, $maitreOuvrage = null, $maitreOuvrageDelegue = null, $maitreOeuvre = null, $idControleSurveillance = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $numeroContrat = null, $dateContrat = null, $dateOrdreService = null, $idTitulaire = null, $resultatTravaux = null, $motifRuptureContrat = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null, $bailleurTravaux = null, $precisionConsistance = null, $precisionModePassation = null)
    {   
        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $sql = "INSERT into t_vn_05_travaux (id_infrastructure, objet, consistance_travaux, maitre_ouvrage, maitre_ouvrage_delegue, maitre_oeuvre, id_controle_surveillance, mode_passation, porte_appel_offre, montant, numero_contrat, date_contrat, date_ordre_service, id_titulaire, resultat_travaux, motif_rupture_contrat, date_reception_provisoire, date_reception_definitive, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_information, source_information, mode_acquisition_information, bailleur, precision_consistance, precision_mode_passation ) VALUES (".intval($idInfrastructure).", '".$objet."', '".$consistanceTravaux."', '".$maitreOuvrage."', '".$maitreOuvrageDelegue."', '".$maitreOeuvre."', ".intval($idControleSurveillance).", '".$modePassation."', '".$porteAppelOffre."', ".intval($montant).", '".$numeroContrat."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdreService->format("Y-m-d")."', ".intval($idTitulaire).", '".$resultatTravaux."', '".$motifRuptureContrat."','".$dateReceptionProvisoire->format("Y-m-d")."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$bailleurTravaux."', '".$precisionConsistance."', '".$precisionModePassation."')";
     
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteFourniture($objetContrat = null, $consistanceContrat = null, $materiels = null, $entite = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $idTitulaire = null, $numeroContrat = null, $dateContrat = null, $dateOrdre = null, $resultat = null, $raisonResiliation = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $idInfrastructure = null, $bailleur = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null, $precisionModePassation = null)
    {   
        $sql = "INSERT into t_vn_07_fourniture (id_infrastructure, objet, consistance_contrat, materiel_concerne, entite, id_titulaire, mode_passation, porte_appel_offre, montant, numero_contrat, date_contrat, date_ordre_service, resultat_service, motif_rupture_contrat, date_reception_provisoire, date_reception_definitive, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_information, source_information, mode_acquisition_information, bailleur, precision_mode_passation) VALUES ('".intval($idInfrastructure)."', '".$objetContrat."', '".$consistanceContrat."', '".$materiels."', '".$entite."', ".intval($idTitulaire).", '".$modePassation."', '".$porteAppelOffre."', ".intval($montant).",  '".$numeroContrat."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdre->format("Y-m-d")."', '".$resultat."', '".$raisonResiliation."', '".$dateReceptionProvisoire->format("Y-m-d")."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$bailleur."', '".$precisionModePassation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEtudes($idInfrastructure = null, $objetContrat = null, $consistanceContrat = null, $entite = null, $idTitulaire = null, $montantContrat = null, $numeroContrat = null, $modePassation = null, $porteAppelOffre = null, $dateContrat = null, $dateOrdreService = null, $resultatPrestation = null, $motifRuptureContrat = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null, $bailleur = null, $precisionConsistance = null, $precisionModePassation = null )
    {   
        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $sql = "INSERT into t_vn_07_etudes (id_infrastructure, objet_contrat, consistance_contrat, entite, id_titulaire, montant_contrat, numero_contrat, mode_passation, porte_appel_offre, date_contrat, date_ordre_service, resultat_prestation, motif_rupture_contrat, date_information, source_information, mode_acquisition_information, bailleur, precision_consistance, precision_mode_passation) VALUES (".intval($idInfrastructure).", '".$objetContrat."', '".$consistanceContrat."', '".$entite."', ".intval($idTitulaire).", ".intval($montantContrat).", '".$numeroContrat."', '".$modePassation."', '".$porteAppelOffre."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdreService->format("Y-m-d")."', '".$resultatPrestation."', '".$motifRuptureContrat."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$bailleur."', '".$precisionConsistance."', '".$precisionModePassation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }


    /*public function getAllyRouteInfo()
    {
        $sql = "select route.gid as id, ST_ASGeoJSON(route.geom) AS geom, route.nom as nom from y_liste_route as route";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
       
    }*/

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