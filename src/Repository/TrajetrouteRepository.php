<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class TrajetrouteRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager("middleware");
    }

    public function addInfrastructure($nom = null, $nom_de_la_route_a_qui_il_est_rattache = null, $localite_depart = null, $localite_arrive = null, $pk_depart = null, $pk_arrive = null, $categorie = null, $sourceInformation = null, $modeAcquisitionInformation = null, $coordonnees = null, $photo1 = null, $photo2 = null, $photo3 = null, $photo_name1 = null, $photo_name2 = null, $photo_name3 = null )
    {
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_tj_01_infrastructure (geom, nom, nom_de_la_route_a_qui_il_est_rattache, localite_depart, localite_arrive, pk_depart, pk_arrive, categorie, date_information, source_information, mode_acquisition_information, photo1, photo2, photo3, photo_name1, photo_name2, photo_name3) VALUES (ST_GeomFromText('MULTILINESTRING((".$coordonnees."))'), '".$nom."', '".$nom_de_la_route_a_qui_il_est_rattache."', '".$localite_depart."', '".$localite_arrive."', ".intval($pk_depart).", ".intval($pk_arrive).", '".$categorie."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', 4326), '".$photo1."', '".$photo2."', '".$photo3."', '".$photo_name1."', '".$photo_name2."', '".$photo_name3."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructurePhoto($idInfra = null, $photo1 = null, $photo2 = null, $photo3 = null, $photo_name1 = null, $photo_name2 = null, $photo_name3 = null )
    {
        $sql = "UPDATE t_tj_01_infrastructure SET photo1 = '".$photo1."', photo2 = '".$photo2."', photo3 = '".$photo3."', photo_name1 = '".$photo_name1."', photo_name2 = '".$photo_name2."', photo_name3 = '".$photo_name3."' where id = ".$idInfra."";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->executeQuery();
     
        return $idInfra;
    }

    public function getAllInfrastructures()
    {
        $sql = 'SELECT infra.id as infra_id, infra.nom as nom, infra.nom_de_la_route_a_qui_il_est_rattache, infra.localite_depart, infra.localite_arrive, infra.pk_depart, infra.pk_arrive, infra.categorie, infra.date_information, infra.source_information, infra.mode_acquisition_information,  infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3, situation.id as situation__id, situation.id_infrastructure as situation__id_infrastructure, situation.fonctionnel as situation__fonctionnel, situation.raison as situation__raison, situation.date_information as situation__date_information, situation.source_information as situation__source_information, situation.mode_acquisition_information as situation__mode_acquisition_information, situation.etat as situation__etat, dc.id as data__id, dc.id_infrastructure as data__id_infrastructure, dc.praticable_toute_l_annee as data__praticable_toute_l_annee, dc.mois_d_ouverture as data__mois_d_ouverture, dc.mois_de_fermeture as data__mois_de_fermeture, dc.duree_trajet_en_saison_seche as data__duree_trajet_en_saison_seche, dc.duree_trajet_en_saison_de_pluie as data__duree_trajet_en_saison_de_pluie, dc.date_information as data__date_information, dc.source_information as data__source_information, dc.mode_acquisition_information as data__mode_acquisition_information, dc.revetement as data__revetement, trav.id as travaux__trav, trav.objet as travaux__objet, trav.consistance_travaux as travaux__consistance_travaux, trav.mode_realisation as travaux__mode_realisation, trav.maitre_ouvrage as travaux__maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux__maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux__maitre_oeuvre, trav.id_controle_surveillance as travaux__id_controle_surveillance, tra.bailleur as travaux__bailleur,trav.mode_passation as travaux__mode_passation, trav.porte_appel_offre as travaux__porte_appel_offre, trav.id_titulaire as travaux__id_titulaire, trav.montant as travaux__montant, trav.date_contrat as travaux__date_contrat, trav.numero_contrat as travaux__numero_contrat, trav.date_ordre_service as travaux__date_ordre_service, trav.resultat_travaux as travaux__resultat_travaux, trav.motif_rupture_contrat as travaux__motif_rupture_contrat, trav.date_reception_provisoire as travaux__date_reception_provisoire, trav.date_reception_definitive as travaux__date_reception_definitive, trav.ingenieur_reception_provisoire as travaux__ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux__ingenieur_reception_definitive, trav.id_infrastructure as travaux__id_infrastructure, trav.date_information as travaux__date_information, trav.source_information as travaux__source_information, trav.mode_acquisition_information as travaux__mode_acquisition_information, trav.precision_consitance as travaux__precision_consitance, trav.precision_passation as travaux__precision_passation, etude.objet_contrat as etude__objet_contrat, etude.consistance_contrat as etude__consistance_contrat, etude.entite as etude__entite, etude.bailleur as etude__bailleur, etude.mode_passation as etude__mode_passation, etude.porte_appel_offre as etude__porte_appel_offre, etude.id_titulaire as etude__id_titulaire, etude.montant as etude__montant, etude.date_contrat as etude__date_contrat, etude.numero_contrat as etude__numero_contrat, etude.date_ordre_service as etude__date_ordre_service, etude.resultat_prestation as etude__resultat_prestation, etude.motif_rupture_contrat as etude__motif_rupture_contrat, etude.id_infrastructure as etude__id_infrastructure, etude.precision_consistance as etude__precision_consistance, etude.precision_passation as etude__precision_passation, f.id as fourniture__id, f.materiels as fourniture__materiels, f.entite as fourniture__entite, f.bailleur as fourniture__bailleur, f.mode_passation as fourniture__mode_passation, f.porte_appel_offre as fourniture__porte_appel_offre, f.id_titulaire as fourniture__id_titulaire, f.montant as fourniture__montant, f.date_contrat as fourniture__date_contrat, f.numero_contrat as fourniture__numero_contrat, f.date_ordre as fourniture__date_ordre, f.resultat as fourniture__resultat, f.raison_resiliation as fourniture__raison_resiliation, f.date_reception_provisoire as fourniture__date_reception_provisoire, f.ingenieur_reception_provisoire as fourniture__ingenieur_reception_provisoire, f.date_reception_definitive as fourniture__date_reception_definitive, f.ingenieur_reception_definitive as fourniture__ingenieur_reception_definitive, f.id_infrastructure as fournirture__id_infrastructure, f.consistance_contrat as fourniture__consistance_contrat, f.precision_passation as fourniture__precision_passation  FROM t_tj_01_infrastructure as infra LEFT JOIN t_tj_02_situation as situation ON infra.id = situation.id_infrastructure  LEFT JOIN t_tj_04_donnees_collectees as dc ON infra.id = dc.id_infrastructure  LEFT JOIN t_tj_05_travaux as trav ON infra.id = trav.id_infrastructure LEFT JOIN t_tj_06_fourniture as f ON infra.id = f.id_infrastructure LEFT JOIN t_tj_07_etudes as etude ON infra.id = etude.id_infrastructure';

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getAllInfrastructuresMinifie()
    {
        $sql = 'SELECT infra.id as infra_id, infra.nom as nom, infra.nom_de_la_route_a_qui_il_est_rattache, infra.localite_depart, infra.localite_arrive, infra.pk_depart, infra.pk_arrive, infra.categorie, infra.date_information, infra.source_information, infra.mode_acquisition_information,  infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3  FROM t_pnr_01_infrastructure as infra';

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getOneInfraInfo($infraId)
    {
        $sql = "SELECT infra.id as infra_id, infra.nom as nom, infra.nom_de_la_route_a_qui_il_est_rattache, infra.localite_depart, infra.localite_arrive, infra.pk_depart, infra.pk_arrive, infra.categorie, infra.date_information, infra.source_information, infra.mode_acquisition_information,  infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3, situation.id as situation__id, situation.id_infrastructure as situation__id_infrastructure, situation.fonctionnel as situation__fonctionnel, situation.raison as situation__raison, situation.date_information as situation__date_information, situation.source_information as situation__source_information, situation.mode_acquisition_information as situation__mode_acquisition_information, situation.etat as situation__etat, dc.id as data__id, dc.id_infrastructure as data__id_infrastructure, dc.praticable_toute_l_annee as data__praticable_toute_l_annee, dc.mois_d_ouverture as data__mois_d_ouverture, dc.mois_de_fermeture as data__mois_de_fermeture, dc.duree_trajet_en_saison_seche as data__duree_trajet_en_saison_seche, dc.duree_trajet_en_saison_de_pluie as data__duree_trajet_en_saison_de_pluie, dc.date_information as data__date_information, dc.source_information as data__source_information, dc.mode_acquisition_information as data__mode_acquisition_information, dc.revetement as data__revetement, trav.id as travaux__trav, trav.objet as travaux__objet, trav.consistance_travaux as travaux__consistance_travaux, trav.mode_realisation as travaux__mode_realisation, trav.maitre_ouvrage as travaux__maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux__maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux__maitre_oeuvre, trav.id_controle_surveillance as travaux__id_controle_surveillance, tra.bailleur as travaux__bailleur,trav.mode_passation as travaux__mode_passation, trav.porte_appel_offre as travaux__porte_appel_offre, trav.id_titulaire as travaux__id_titulaire, trav.montant as travaux__montant, trav.date_contrat as travaux__date_contrat, trav.numero_contrat as travaux__numero_contrat, trav.date_ordre_service as travaux__date_ordre_service, trav.resultat_travaux as travaux__resultat_travaux, trav.motif_rupture_contrat as travaux__motif_rupture_contrat, trav.date_reception_provisoire as travaux__date_reception_provisoire, trav.date_reception_definitive as travaux__date_reception_definitive, trav.ingenieur_reception_provisoire as travaux__ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux__ingenieur_reception_definitive, trav.id_infrastructure as travaux__id_infrastructure, trav.date_information as travaux__date_information, trav.source_information as travaux__source_information, trav.mode_acquisition_information as travaux__mode_acquisition_information, trav.precision_consitance as travaux__precision_consitance, trav.precision_passation as travaux__precision_passation, etude.objet_contrat as etude__objet_contrat, etude.consistance_contrat as etude__consistance_contrat, etude.entite as etude__entite, etude.bailleur as etude__bailleur, etude.mode_passation as etude__mode_passation, etude.porte_appel_offre as etude__porte_appel_offre, etude.id_titulaire as etude__id_titulaire, etude.montant as etude__montant, etude.date_contrat as etude__date_contrat, etude.numero_contrat as etude__numero_contrat, etude.date_ordre_service as etude__date_ordre_service, etude.resultat_prestation as etude__resultat_prestation, etude.motif_rupture_contrat as etude__motif_rupture_contrat, etude.id_infrastructure as etude__id_infrastructure, etude.precision_consistance as etude__precision_consistance, etude.precision_passation as etude__precision_passation, f.id as fourniture__id, f.materiels as fourniture__materiels, f.entite as fourniture__entite, f.bailleur as fourniture__bailleur, f.mode_passation as fourniture__mode_passation, f.porte_appel_offre as fourniture__porte_appel_offre, f.id_titulaire as fourniture__id_titulaire, f.montant as fourniture__montant, f.date_contrat as fourniture__date_contrat, f.numero_contrat as fourniture__numero_contrat, f.date_ordre as fourniture__date_ordre, f.resultat as fourniture__resultat, f.raison_resiliation as fourniture__raison_resiliation, f.date_reception_provisoire as fourniture__date_reception_provisoire, f.ingenieur_reception_provisoire as fourniture__ingenieur_reception_provisoire, f.date_reception_definitive as fourniture__date_reception_definitive, f.ingenieur_reception_definitive as fourniture__ingenieur_reception_definitive, f.id_infrastructure as fournirture__id_infrastructure, f.consistance_contrat as fourniture__consistance_contrat, f.precision_passation as fourniture__precision_passation  FROM t_tj_01_infrastructure as infra as infra LEFT JOIN t_tj_02_situation as situation ON infra.id = situation.id_infrastructure  LEFT JOIN t_tj_04_donnees_collectees as dc ON infra.id = dc.id_infrastructure  LEFT JOIN t_tj_05_travaux as trav ON infra.id = trav.id_infrastructure LEFT JOIN t_tj_06_fourniture as f ON infra.id = f.id_infrastructure LEFT JOIN t_tj_07_etudes as etude ON infra.id = etude.id_infrastructure where infra.id = ".$infraId."";

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
    */
    public function addInfrastructureRouteEtat($idInfrastructure = null, $etat = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_tj_03_etat (id_infrastructure, etat, date_information, source_information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$etat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        if (null != $idInfrastructure && $type != null) {

            $table = 't_tj_01_infrastructure';
            $colonne = "id";
            $selectedcolonne = "id";
            switch ($type) {
                case 'situation':
                    $table = "t_tj_02_situation";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                /*case 'surface':
                    $table = "t_ro_04_surface";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;*/
                case 'data':
                    $table = "t_tj_04_donnees_collectees";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'etat':
                    $table = "t_tj_03_etat";
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
                    $table = "t_tj_05_travaux";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'fourniture':
                    $table = "t_tj_06_fourniture";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'etude':
                    $table = "t_tj_07_etudes";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                default:
                    $table = 't_tj_01_infrastructure';
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
        $sql = "UPDATE t_pnr_01_infrastructure SET ".$updateColonneInfra." where id = ".$idInfra."";
       
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
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_tj_02_situation (id_infrastructure, fonctionnel, raison, date_information, source_information, mode_acquisition_information, etat) VALUES (".intval($idInfrastructure).", '".$fonctionnel."', '".$motif."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."', '".$etat."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureDonneCollecte($idInfrastructure = null, $praticable_toute_l_annee = null, $mois_d_ouverture = null, $mois_de_fermeture = null, $duree_trajet_en_saison_seche = null,  $duree_trajet_en_saison_de_pluie = null,  $sourceInformation = null,  $modeAcquisitionInformation = null,  $revetement = null, $dateInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_tj_04_donnees_collectees (id_infrastructure, praticable_toute_l_annee, mois_d_ouverture, mois_de_fermeture, duree_trajet_en_saison_seche, duree_trajet_en_saison_de_pluie, date_information, source_information, mode_acquisition_information, revetement) VALUES (".intval($idInfrastructure).", '".$praticable_toute_l_annee."', '".$mois_d_ouverture."', '".$mois_de_fermeture."', '".$duree_trajet_en_saison_seche."', '".$duree_trajet_en_saison_de_pluie."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$revetement."')";
        
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

    public function addInfrastructureTravaux($idInfrastructure = null, $objet = null, $consistanceTravaux = null, $modeRealisation = null, $maitreOuvrage = null, $maitreOuvrageDelegue = null, $maitreOeuvre = null, $idControleSurveillance = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $numeroContrat = null, $dateContrat = null, $dateOrdreService = null, $idTitulaire = null, $resultatTravaux = null, $motifRuptureContrat = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null, $bailleurTravaux = null, $precisionConsitance = null, $precisionPassation = null)
    {   
        $sql = "INSERT into t_tj_05_travaux (objet, consistance_travaux, mode_realisation, maitre_ouvrage, maitre_ouvrage_delegue, maitre_oeuvre, id_controle_surveillance, bailleur, mode_passation, porte_appel_offre, id_titulaire, montant, date_contrat, numero_contrat, date_ordre_service, resultat_travaux, motif_rupture_contrat, date_reception_provisoire, ingenieur_reception_provisoire, date_reception_definitive, ingenieur_reception_definitive, id_infrastructure, date_information, source_information, mode_acquisition_information, precision_consitance, precision_passation) VALUES ('".$objet."', '".$consistanceTravaux."', '".$modeRealisation."', '".$maitreOuvrage."', '".$maitreOuvrageDelegue."', '".$maitreOeuvre."', ".intval($idControleSurveillance).", '".$bailleurTravaux."', '".$modePassation."', '".$porteAppelOffre."', ".intval($idTitulaire).", ".intval($montant).", '".$dateContrat->format("Y-m-d")."', '".$numeroContrat."', '".$dateOrdreService->format("Y-m-d")."', '".$resultatTravaux."', '".$motifRuptureContrat."','".$dateReceptionProvisoire->format("Y-m-d")."', '".$ingenieurReceptionProvisoire."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".$ingenieurReceptionDefinitive."', ".intval($idInfrastructure).", '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$precisionConsitance."', '".$precisionPassation."')";
     
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

   public function addInfrastructureRouteFourniture($objetContrat = null, $consistanceContrat = null, $entite = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $idTitulaire = null, $numeroContrat = null, $dateContrat = null, $dateOrdre = null, $resultat = null, $raisonResiliation = null, $idInfrastructure = null, $bailleur = null, $precisionConsitance = null, $precisionPassation = null)
    {   
        $sql = "INSERT into t_tj_06_fourniture (objet_contrat, materiels, entite, bailleur, mode_passation, porte_appel_offre, id_titulaire, montant, date_contrat, numero_contrat, date_ordre, resultat, raison_resiliation, date_reception_provisoire, ingenieur_reception_provisoire, date_reception_definitive, ingenieur_reception_definitive, id_infrastructure, consistance_contrat, precision_passation) VALUES ('".$objetContrat."', '".$consistanceContrat."', '".$entite."', '".$bailleur."', '".$modePassation."', '".$porteAppelOffre."', ".intval($idTitulaire).", ".intval($montant).", '".$dateContrat->format("Y-m-d")."', '".$numeroContrat."', '".$dateOrdre->format("Y-m-d")."', '".$resultat."', '".$raisonResiliation."', '".intval($idInfrastructure)."', '".$precisionConsitance."', '".$precisionPassation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureEtudes($idInfrastructure = null, $objetContrat = null, $consistanceContrat = null, $entite = null, $idTitulaire = null, $montantContrat = null, $numeroContrat = null, $modePassation = null, $porteAppelOffre = null, $dateContrat = null, $dateOrdreService = null, $resultatPrestation = null, $motifRuptureContrat = null, $bailleur = null, $precisionConsitance = null, $precisionPassation = null)
    {   
        $sql = "INSERT into t_tj_07_etudes (objet_contrat, consistance_contrat, entite, bailleur, mode_passation, porte_appel_offre, id_titulaire, montant, date_contrat, numero_contrat, date_ordre_service, resultat_prestation, motif_rupture_contrat, id_infrastructure, precision_consistance, precision_passation) VALUES (".intval($idInfrastructure).", '".$objetContrat."', '".$consistanceContrat."', '".$entite."', '".$bailleur."', '".$modePassation."', '".$porteAppelOffre."', ".intval($idTitulaire).", ".intval($montantContrat).", '".$dateContrat->format("Y-m-d")."', '".$numeroContrat."', '".$dateOrdreService->format("Y-m-d")."', '".$resultatPrestation."', '".$motifRuptureContrat."', ".intval($idInfrastructure).", '".$precisionConsitance."', '".$precisionPassation."')";
        
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