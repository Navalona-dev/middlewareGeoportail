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

    public function addInfrastructureRoute($categorie = null, $localite = null, $sourceInformation = null, $modeAcquisitionInformation = null, $communeTerrain = null, $pkDebut = null, $rattache = null, $gestionnaire = null, $modeGestion = null, $pkFin = null, $largeurHausse = null, $largeurAccotement = null,$structure = null, $region = null, $district = null, $longitude = null, $latitude = null, $photo1 = null, $photo2 = null, $photo3 = null, $precisionStructure = null, $precisionModeGestion = null, $photo_name1 = null, $photo_name2 = null, $photo_name3 = null )
    {
        $dateInfo = new \DateTime();
        $localite = pg_escape_string($localite);
        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $modeGestion = pg_escape_string($modeGestion);
        $precisionModeGestion = pg_escape_string($precisionModeGestion);
        $sql = "INSERT into t_ro_01_infrastructure (pk_debut, rattache, categorie, localite,  commune_terrain, gestionnaire, mode_gestion, date_information, source_Information, mode_acquisition_infromation, pk_fin, \"Largeur_chaussée\", \"Largeur_accotements\", \"Structure\", region, district, geom, photo1, photo2, photo3, precision_structure, precision_mode_gestion, photo_name1, photo_name2, photo_name3) VALUES (".intval($pkDebut).", '".$rattache."', '".$categorie."', '".$localite."', '".$communeTerrain."', '".$gestionnaire."', '".$modeGestion."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', ".intval($pkFin).", ".floatval($largeurHausse).", ".floatval($largeurAccotement).", '".$structure."', '".$region."', '".$district."', ST_GeomFromText('POINT(" . $longitude . " " . $latitude . ")', 4326), '".$photo1."', '".$photo2."', '".$photo3."', '".$precisionStructure."', '".$precisionModeGestion."', '".$photo_name1."', '".$photo_name2."', '".$photo_name3."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }
    
    public function addInfrastructurePhoto($idInfra = null, $setUpdate )
    {
        $sql = "UPDATE t_ro_01_infrastructure SET ".$setUpdate." where id = ".$idInfra."";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->executeQuery();
     
        return $idInfra;
    }

    public function getPhotoInfraInfo($infraId)
    {
        $sql = "SELECT infra.id as infra_id, infra.photo1, infra.photo2, infra.photo3, infra.photo_name1, infra.photo_name2, infra.photo_name3 FROM t_ro_01_infrastructure as infra  where infra.id = ".intval($infraId)."";

        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getAllInfrastructuresRoute()
    {
       // $sql = 'SELECT infraroute.gid, infraroute.pk_debut, infraroute.rattache, infraroute.categorie, infraroute.localite, infraroute.commune_terrain, infraroute.gestionnaire, infraroute.mode_gestion, infraroute.date_information, infraroute.source_information, infraroute.mode_acquisition_infromation, infraroute.pk_fin, infraroute."Largeur_chaussée", infraroute."Largeur_accotements", infraroute.region, infraroute.district,  ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat, infraroute.photo_name1, infraroute.photo_name2, infraroute.photo_name3, s.etat as etat, s.fonctionnel as situation_fonctionnel, s.raison, s.date_information as situation_data_info, s.source_Information as situation_src_info, s.mode_acquisition_information as situation_mode_aquis_info, srfc.revetement as surface_revetement, srfc.revetue_nid_de_poule as surface_revetue_nid_de_poule, srfc.revetue_arrachement as surface_revetue_arrachement, srfc.revetue_ressuage as surface_revetue_ressuage, srfc.revetue_fissure_logitudinale_de_joint as surface_fissure_logitudinale_de_joint, srfc.non_revetue_traverse as surface_non_revetue_traverse, srfc.non_revetue_bourbier as surface_non_revetue_bourbier, srfc.non_revetue_tete_de_chat as surface_non_revetue_tete_de_chat, srfc.date_information as surface_date_information, srfc.source_Information as surface_source_Information, srfc.mode_acquisition_infromation as surface_mode_acquisition_infromation, str.revetue_defomation as structure_revetue_defomation, str.revetue_fissuration as structure_revetue_fissuration, str.revetue_faiencage as structure_revetue_faiencage, str.non_revetue_nids_de_poule as structure_non_revetue_nids_de_poule, str.non_revetue_deformation as structure_non_revetue_deformation, str.non_revetue_tole_ondule as structure_non_revetue_tole_ondule, str.non_revetue_ravines as structure_non_revetue_ravines, str.date_information as structure_date_information, str.source_Information as structure_source_Information,  str.mode_acquisition_information as structure_mode_acquisition_information, fonc."Statut" as statut_foncier, fonc.numero_de_reference as numero_de_reference_foncier, fonc.nom_proprietaire as nom_proprietaire_foncier, trav.bailleur as bailleur_travaux, trav.objet as travaux_objet, trav.consistance_travaux as travaux_consistance_travaux, trav.mode_realisation_travaux as travaux_mode_realisation_travaux, trav.maitre_ouvrage as travaux_maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux_maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux_maitre_oeuvre, trav.id_controle_surveillance as travaux_id_controle_surveillance, trav.mode_passation as travaux_mode_passation, trav.porte_appel_offre as travaux_porte_appel_offre, trav.montant as travaux_montant, trav.numero_contrat as travaux_numero_contrat, trav.date_contrat as travaux_date_contrat, trav.date_ordre_service as travaux_date_ordre_service, trav.id_titulaire as travaux_id_titulaire, trav.resultat_travaux as travaux_resultat_travaux, trav.motif_rupture_contrat as travaux_motif_rupture_contrat, trav.date_reception_provisoire as travaux_date_reception_provisoire, trav.date_reception_definitive as travaux_date_reception_definitive, trav.ingenieur_reception_provisoire as travaux_ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux_ingenieur_reception_definitive, trav.date_information as travaux_date_information, trav.source_information as travaux_source_information, trav.mode_acquisition_information as travaux_mode_acquisition_information, f.objet_contrat as fourniture_objet_contrat, f.consistance_contrat as fourniture_consistance_contrat, f.materiels as fourniture_materiels, f.entite as fourniture_entite, f.mode_passation as fourniture_mode_passation, f.porte_appel_offre as fourniture_porte_appel_offre, f.montant as fourniture_montant, f.id_titulaire as fourniture_id_titulaire, f.numero_contrat as fourniture_numero_contrat, f.date_contrat as fourniture_date_contrat, f.date_ordre as fourniture_date_ordre, f.resultat as fourniture_resultat, f.raison_resiliation as fourniture_raison_resiliation, f.ingenieur_reception_provisoire as fourniture_ingenieur_reception_provisoire, f.ingenieur_reception_definitive as fourniture_ingenieur_reception_definitive, f.date_reception_provisoire as fourniture_date_reception_provisoire, f.date_reception_definitive as fourniture_date_reception_definitive, f.bailleur as bailleur_fourniture, et.precision_consistance_contrat as precision_consistance_contrat_etude, et.precision_consistance_contrat as precision_consistance_contrat_etude, et.bailleur as bailleur_etude, et.objet_contrat as etude_objet_contrat, et.consistance_contrat as etude_consistance_contrat, et.entite as etude_entite, et.id_titulaire as etude_id_titulaire, et.montant_contrat as etude_montant_contrat, et.numero_contrat as etude_numero_contrat, et.mode_passation as etude_mode_passation, et.porte_appel_offre as etude_porte_appel_offre, et.date_contrat as etude_date_contrat, et.date_ordre_service as etude_date_ordre_service, et.resultat_prestation as etude_resultat_prestation, et.motif_rupture_contrat as etude_motif_rupture_contrat, et.date_information as etude_date_information, et.source_information as etude_source_information, et.mode_acquisition_information as etude_mode_acquisition_information, accote.cote as cote_accote, accote.revetue_degradation_de_la_surface as revetue_degradation_de_la_surface_accote, accote.revetue_dentelle_de_rive as revetue_dentelle_de_rive_accote, accote.revetue_denivellation_entre_chaussée_et_accotement as revetue_denivellation_entre_chaussée_et_accotement, accote.revetue_destruction_par_affouillement_de_accotement as revetue_destruction_par_affouillement_de_accotement, accote.non_revetue_deformation_du_profil as non_revetue_deformation_du_profil_accote, accote.revetu as revetu_accote, accote.date_information as date_information_accote, accote.source_information as source_information_accote, accote.mode_acquisition_information as mode_acquisition_information_accote, fosse.cote as cote_fosse, fosse.revetue_degradation_du_fosse as revetue_degradation_du_fosse, fosse.revetue_section_bouche as revetue_section_bouche_fosse, fosse.non_revetue_profil as non_revetue_profil_fosse, fosse.non_revetue_encombrement as non_revetue_encombrement_fosse, fosse.revetu as revetu_fosse, fosse.date_information as date_information_fosse, fosse.source_information as source_information_fosse, fosse.mode_acquisition_information as mode_acquisition_information_fosse   FROM t_ro_01_infrastructure as infraroute LEFT JOIN t_ro_02_situation as s ON infraroute.gid = s.id_infrastructure  LEFT JOIN t_ro_04_surface as srfc ON infraroute.gid = srfc.id_infrastructure  LEFT JOIN t_ro_05_structure as str ON infraroute.gid = str.id_infrastructure LEFT JOIN t_ro_13_foncier as fonc ON infraroute.gid = fonc.id_infrastructure LEFT JOIN t_ro_09_travaux as trav ON infraroute.gid = trav.id_infrastructure LEFT JOIN t_ro_14_fourniture as f ON infraroute.gid = f.id_infrastructure LEFT JOIN t_ro_11_etudes as et ON infraroute.gid = et.id_infrastructure LEFT JOIN t_ro_07_accotement as accote ON infraroute.gid = accote.id_infrastructure LEFT JOIN t_ro_08_fosse as fosse ON infraroute.gid = fosse.id_infrastructure';
        $sql = 'SELECT infraroute.gid, infraroute.pk_debut, infraroute.rattache, infraroute.categorie, infraroute.localite, infraroute.commune_terrain, infraroute.gestionnaire, infraroute.mode_gestion, infraroute.date_information, infraroute.source_information, infraroute.mode_acquisition_infromation, infraroute.pk_fin, infraroute."Largeur_chaussée", infraroute."Largeur_accotements", infraroute."Structure", infraroute.region, infraroute.district,  ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat,infraroute.photo1, infraroute.photo2, infraroute.photo3, infraroute.photo_name1, infraroute.photo_name2, infraroute.photo_name3, s.etat as etat, s.fonctionnel as situation_fonctionnel, s.raison, s.date_information as situation_data_info, s.source_Information as situation_src_info, s.mode_acquisition_information as situation_mode_aquis_info, srfc.revetement as surface_revetement, srfc.revetue_nid_de_poule as surface_revetue_nid_de_poule, srfc.revetue_arrachement as surface_revetue_arrachement, srfc.revetue_ressuage as surface_revetue_ressuage, srfc.revetue_fissure_logitudinale_de_joint as surface_fissure_logitudinale_de_joint, srfc.non_revetue_traverse as surface_non_revetue_traverse, srfc.non_revetue_bourbier as surface_non_revetue_bourbier, srfc.non_revetue_tete_de_chat as surface_non_revetue_tete_de_chat, srfc.date_information as surface_date_information, srfc.source_Information as surface_source_Information, srfc.mode_acquisition_infromation as surface_mode_acquisition_infromation, str.revetue_defomation as structure_revetue_defomation, str.revetue_fissuration as structure_revetue_fissuration, str.revetue_faiencage as structure_revetue_faiencage, str.non_revetue_nids_de_poule as structure_non_revetue_nids_de_poule, str.non_revetue_deformation as structure_non_revetue_deformation, str.non_revetue_tole_ondule as structure_non_revetue_tole_ondule, str.non_revetue_ravines as structure_non_revetue_ravines, str.date_information as structure_date_information, str.source_Information as structure_source_Information,  str.mode_acquisition_information as structure_mode_acquisition_information, fonc."Statut" as statut_foncier, fonc.numero_de_reference as numero_de_reference_foncier, fonc.nom_proprietaire as nom_proprietaire_foncier, trav.bailleur as bailleur_travaux, trav.objet as travaux_objet, trav.consistance_travaux as travaux_consistance_travaux, trav.mode_realisation_travaux as travaux_mode_realisation_travaux, trav.maitre_ouvrage as travaux_maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux_maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux_maitre_oeuvre, trav.id_controle_surveillance as travaux_id_controle_surveillance, trav.mode_passation as travaux_mode_passation, trav.porte_appel_offre as travaux_porte_appel_offre, trav.montant as travaux_montant, trav.numero_contrat as travaux_numero_contrat, trav.date_contrat as travaux_date_contrat, trav.date_ordre_service as travaux_date_ordre_service, trav.id_titulaire as travaux_id_titulaire, trav.resultat_travaux as travaux_resultat_travaux, trav.motif_rupture_contrat as travaux_motif_rupture_contrat, trav.date_reception_provisoire as travaux_date_reception_provisoire, trav.date_reception_definitive as travaux_date_reception_definitive, trav.ingenieur_reception_provisoire as travaux_ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux_ingenieur_reception_definitive, trav.date_information as travaux_date_information, trav.source_information as travaux_source_information, trav.mode_acquisition_information as travaux_mode_acquisition_information, f.objet_contrat as fourniture_objet_contrat, f.consistance_contrat as fourniture_consistance_contrat, f.materiels as fourniture_materiels, f.entite as fourniture_entite, f.mode_passation as fourniture_mode_passation, f.porte_appel_offre as fourniture_porte_appel_offre, f.montant as fourniture_montant, f.id_titulaire as fourniture_id_titulaire, f.numero_contrat as fourniture_numero_contrat, f.date_contrat as fourniture_date_contrat, f.date_ordre as fourniture_date_ordre, f.resultat as fourniture_resultat, f.raison_resiliation as fourniture_raison_resiliation, f.ingenieur_reception_provisoire as fourniture_ingenieur_reception_provisoire, f.ingenieur_reception_definitive as fourniture_ingenieur_reception_definitive, f.date_reception_provisoire as fourniture_date_reception_provisoire, f.date_reception_definitive as fourniture_date_reception_definitive, f.bailleur as bailleur_fourniture, et.precision_consistance_contrat as precision_consistance_contrat_etude, et.precision_consistance_contrat as precision_consistance_contrat_etude, et.bailleur as bailleur_etude, et.objet_contrat as etude_objet_contrat, et.consistance_contrat as etude_consistance_contrat, et.entite as etude_entite, et.id_titulaire as etude_id_titulaire, et.montant_contrat as etude_montant_contrat, et.numero_contrat as etude_numero_contrat, et.mode_passation as etude_mode_passation, et.porte_appel_offre as etude_porte_appel_offre, et.date_contrat as etude_date_contrat, et.date_ordre_service as etude_date_ordre_service, et.resultat_prestation as etude_resultat_prestation, et.motif_rupture_contrat as etude_motif_rupture_contrat, et.date_information as etude_date_information, et.source_information as etude_source_information, et.mode_acquisition_information as etude_mode_acquisition_information FROM t_ro_01_infrastructure as infraroute LEFT JOIN t_ro_02_situation as s ON infraroute.gid = s.id_infrastructure  LEFT JOIN t_ro_04_surface as srfc ON infraroute.gid = srfc.id_infrastructure  LEFT JOIN t_ro_05_structure as str ON infraroute.gid = str.id_infrastructure LEFT JOIN t_ro_13_foncier as fonc ON infraroute.gid = fonc.id_infrastructure LEFT JOIN t_ro_09_travaux as trav ON infraroute.gid = trav.id_infrastructure LEFT JOIN t_ro_14_fourniture as f ON infraroute.gid = f.id_infrastructure LEFT JOIN t_ro_11_etudes as et ON infraroute.gid = et.id_infrastructure';
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getAllInfrastructuresRouteMinifie()
    {
       // $sql = 'SELECT infraroute.gid, infraroute.pk_debut, infraroute.rattache, infraroute.categorie, infraroute.localite, infraroute.commune_terrain, infraroute.gestionnaire, infraroute.mode_gestion, infraroute.date_information, infraroute.source_information, infraroute.mode_acquisition_infromation, infraroute.pk_fin, infraroute."Largeur_chaussée", infraroute."Largeur_accotements", infraroute.region, infraroute.district,  ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat, infraroute.photo_name1, infraroute.photo_name2, infraroute.photo_name3, s.etat as etat, s.fonctionnel as situation_fonctionnel, s.raison, s.date_information as situation_data_info, s.source_Information as situation_src_info, s.mode_acquisition_information as situation_mode_aquis_info, srfc.revetement as surface_revetement, srfc.revetue_nid_de_poule as surface_revetue_nid_de_poule, srfc.revetue_arrachement as surface_revetue_arrachement, srfc.revetue_ressuage as surface_revetue_ressuage, srfc.revetue_fissure_logitudinale_de_joint as surface_fissure_logitudinale_de_joint, srfc.non_revetue_traverse as surface_non_revetue_traverse, srfc.non_revetue_bourbier as surface_non_revetue_bourbier, srfc.non_revetue_tete_de_chat as surface_non_revetue_tete_de_chat, srfc.date_information as surface_date_information, srfc.source_Information as surface_source_Information, srfc.mode_acquisition_infromation as surface_mode_acquisition_infromation, str.revetue_defomation as structure_revetue_defomation, str.revetue_fissuration as structure_revetue_fissuration, str.revetue_faiencage as structure_revetue_faiencage, str.non_revetue_nids_de_poule as structure_non_revetue_nids_de_poule, str.non_revetue_deformation as structure_non_revetue_deformation, str.non_revetue_tole_ondule as structure_non_revetue_tole_ondule, str.non_revetue_ravines as structure_non_revetue_ravines, str.date_information as structure_date_information, str.source_Information as structure_source_Information,  str.mode_acquisition_information as structure_mode_acquisition_information, fonc."Statut" as statut_foncier, fonc.numero_de_reference as numero_de_reference_foncier, fonc.nom_proprietaire as nom_proprietaire_foncier, trav.bailleur as bailleur_travaux, trav.objet as travaux_objet, trav.consistance_travaux as travaux_consistance_travaux, trav.mode_realisation_travaux as travaux_mode_realisation_travaux, trav.maitre_ouvrage as travaux_maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux_maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux_maitre_oeuvre, trav.id_controle_surveillance as travaux_id_controle_surveillance, trav.mode_passation as travaux_mode_passation, trav.porte_appel_offre as travaux_porte_appel_offre, trav.montant as travaux_montant, trav.numero_contrat as travaux_numero_contrat, trav.date_contrat as travaux_date_contrat, trav.date_ordre_service as travaux_date_ordre_service, trav.id_titulaire as travaux_id_titulaire, trav.resultat_travaux as travaux_resultat_travaux, trav.motif_rupture_contrat as travaux_motif_rupture_contrat, trav.date_reception_provisoire as travaux_date_reception_provisoire, trav.date_reception_definitive as travaux_date_reception_definitive, trav.ingenieur_reception_provisoire as travaux_ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux_ingenieur_reception_definitive, trav.date_information as travaux_date_information, trav.source_information as travaux_source_information, trav.mode_acquisition_information as travaux_mode_acquisition_information, f.objet_contrat as fourniture_objet_contrat, f.consistance_contrat as fourniture_consistance_contrat, f.materiels as fourniture_materiels, f.entite as fourniture_entite, f.mode_passation as fourniture_mode_passation, f.porte_appel_offre as fourniture_porte_appel_offre, f.montant as fourniture_montant, f.id_titulaire as fourniture_id_titulaire, f.numero_contrat as fourniture_numero_contrat, f.date_contrat as fourniture_date_contrat, f.date_ordre as fourniture_date_ordre, f.resultat as fourniture_resultat, f.raison_resiliation as fourniture_raison_resiliation, f.ingenieur_reception_provisoire as fourniture_ingenieur_reception_provisoire, f.ingenieur_reception_definitive as fourniture_ingenieur_reception_definitive, f.date_reception_provisoire as fourniture_date_reception_provisoire, f.date_reception_definitive as fourniture_date_reception_definitive, f.bailleur as bailleur_fourniture, et.precision_consistance_contrat as precision_consistance_contrat_etude, et.precision_consistance_contrat as precision_consistance_contrat_etude, et.bailleur as bailleur_etude, et.objet_contrat as etude_objet_contrat, et.consistance_contrat as etude_consistance_contrat, et.entite as etude_entite, et.id_titulaire as etude_id_titulaire, et.montant_contrat as etude_montant_contrat, et.numero_contrat as etude_numero_contrat, et.mode_passation as etude_mode_passation, et.porte_appel_offre as etude_porte_appel_offre, et.date_contrat as etude_date_contrat, et.date_ordre_service as etude_date_ordre_service, et.resultat_prestation as etude_resultat_prestation, et.motif_rupture_contrat as etude_motif_rupture_contrat, et.date_information as etude_date_information, et.source_information as etude_source_information, et.mode_acquisition_information as etude_mode_acquisition_information, accote.cote as cote_accote, accote.revetue_degradation_de_la_surface as revetue_degradation_de_la_surface_accote, accote.revetue_dentelle_de_rive as revetue_dentelle_de_rive_accote, accote.revetue_denivellation_entre_chaussée_et_accotement as revetue_denivellation_entre_chaussée_et_accotement, accote.revetue_destruction_par_affouillement_de_accotement as revetue_destruction_par_affouillement_de_accotement, accote.non_revetue_deformation_du_profil as non_revetue_deformation_du_profil_accote, accote.revetu as revetu_accote, accote.date_information as date_information_accote, accote.source_information as source_information_accote, accote.mode_acquisition_information as mode_acquisition_information_accote, fosse.cote as cote_fosse, fosse.revetue_degradation_du_fosse as revetue_degradation_du_fosse, fosse.revetue_section_bouche as revetue_section_bouche_fosse, fosse.non_revetue_profil as non_revetue_profil_fosse, fosse.non_revetue_encombrement as non_revetue_encombrement_fosse, fosse.revetu as revetu_fosse, fosse.date_information as date_information_fosse, fosse.source_information as source_information_fosse, fosse.mode_acquisition_information as mode_acquisition_information_fosse   FROM t_ro_01_infrastructure as infraroute LEFT JOIN t_ro_02_situation as s ON infraroute.gid = s.id_infrastructure  LEFT JOIN t_ro_04_surface as srfc ON infraroute.gid = srfc.id_infrastructure  LEFT JOIN t_ro_05_structure as str ON infraroute.gid = str.id_infrastructure LEFT JOIN t_ro_13_foncier as fonc ON infraroute.gid = fonc.id_infrastructure LEFT JOIN t_ro_09_travaux as trav ON infraroute.gid = trav.id_infrastructure LEFT JOIN t_ro_14_fourniture as f ON infraroute.gid = f.id_infrastructure LEFT JOIN t_ro_11_etudes as et ON infraroute.gid = et.id_infrastructure LEFT JOIN t_ro_07_accotement as accote ON infraroute.gid = accote.id_infrastructure LEFT JOIN t_ro_08_fosse as fosse ON infraroute.gid = fosse.id_infrastructure';
        $sql = 'SELECT infraroute.gid as infra_id, infraroute.pk_debut as pk_debut, infraroute.rattache as rattache, infraroute.categorie, infraroute.localite, infraroute.date_information, infraroute.pk_fin as pk_fin,  ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat,infraroute.photo1, infraroute.photo2, infraroute.photo3 , infraroute.photo_name1, infraroute.photo_name2, infraroute.photo_name3 FROM t_ro_01_infrastructure as infraroute';
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function updateInfrastructure($idInfra = null, $updateColonneInfra = null)
    {
        $dateInfo = new \DateTime();
        $sql = "UPDATE t_ro_01_infrastructure SET ".$updateColonneInfra." where gid = ".$idInfra."";
       
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

    public function getOneInfraInfo($infraId)
    {
        $sql = 'SELECT infraroute.gid, infraroute.pk_debut, infraroute.rattache, infraroute.categorie, infraroute.localite, infraroute.commune_terrain, infraroute.gestionnaire, infraroute.mode_gestion, infraroute.date_information, infraroute.source_information, infraroute.mode_acquisition_infromation, infraroute.pk_fin, infraroute."Largeur_chaussée", infraroute."Largeur_accotements", infraroute."Structure", infraroute.region, infraroute.district,  ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat,infraroute.photo1, infraroute.photo2, infraroute.photo3, infraroute.photo_name1, infraroute.photo_name2, infraroute.photo_name3, s.id as situation__id, s.id_infrastructure as situation__id_infrastructure, s.fonctionnel as situation__fonctionnel, s.raison as situation__raison, s.date_information as situation__date_information, s.source_Information as situation__source_information, s.mode_acquisition_information as situation__mode_acquisition_information, s.etat as situation__etat, srfc.id as surface__id, srfc.id_infrastructure as surface__id_infrastructure, srfc.revetement as surface__revetement, srfc.revetue_nid_de_poule as surface__revetue_nid_de_poule, srfc.revetue_arrachement as surface__revetue_arrachement, srfc.revetue_ressuage as surface__revetue_ressuage, srfc.revetue_fissure_logitudinale_de_joint as surface__fissure_logitudinale_de_joint, srfc.non_revetue_traverse as surface__non_revetue_traverse, srfc.non_revetue_bourbier as surface__non_revetue_bourbier, srfc.non_revetue_tete_de_chat as surface__non_revetue_tete_de_chat, srfc.date_information as surface__date_information, srfc.source_Information as surface__source_Information, srfc.mode_acquisition_infromation as surface__mode_acquisition_infromation, str.id as structure__id, str.id_infrastructure as structure__id_infrastructure, str.revetue_defomation as structure__revetue_defomation, str.revetue_fissuration as structure__revetue_fissuration, str.revetue_faiencage as structure__revetue_faiencage, str.non_revetue_nids_de_poule as structure__non_revetue_nids_de_poule, str.non_revetue_deformation as structure__non_revetue_deformation, str.non_revetue_tole_ondule as structure__non_revetue_tole_ondule, str.non_revetue_ravines as structure__non_revetue_ravines, str.date_information as structure__date_information, str.source_Information as structure__source_Information,  str.mode_acquisition_information as structure__mode_acquisition_information, fonc.id as foncier_id, fonc."Statut" as foncier__Statut, fonc.numero_de_reference as foncier__numero_de_reference, fonc.nom_proprietaire as foncier__nom_proprietaire, fonc.id_infrastructure as foncier__id_infrastructure, trav.id as travaux__id, trav.id_infrastructure as travaux__id_infrastructure, trav.objet as travaux__objet, trav.consistance_travaux as travaux__consistance_travaux, trav.mode_realisation_travaux as travaux__mode_realisation_travaux, trav.maitre_ouvrage as travaux__maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux__maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux__maitre_oeuvre, trav.id_controle_surveillance as travaux__id_controle_surveillance, trav.mode_passation as travaux__mode_passation, trav.porte_appel_offre as travaux__porte_appel_offre, trav.montant as travaux__montant, trav.numero_contrat as travaux__numero_contrat, trav.date_contrat as travaux__date_contrat, trav.date_ordre_service as travaux__date_ordre_service, trav.id_titulaire as travaux__id_titulaire, trav.resultat_travaux as travaux__resultat_travaux, trav.motif_rupture_contrat as travaux__motif_rupture_contrat, trav.date_reception_provisoire as travaux__date_reception_provisoire, trav.date_reception_definitive as travaux__date_reception_definitive, trav.ingenieur_reception_provisoire as travaux__ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux__ingenieur_reception_definitive, trav.date_information as travaux__date_information, trav.source_information as travaux__source_information, trav.mode_acquisition_information as travaux__mode_acquisition_information, trav.precision_consistance as travaux__precision_consistance, trav.mode_realisation as travaux__mode_realisation, trav.bailleur as travaux__bailleur, trav.precision_passation as travaux__precision_passation, f.id as fourniture__id, f.objet_contrat as fourniture__objet_contrat, f.consistance_contrat as fourniture__consistance_contrat, f.materiels as fourniture__materiels, f.entite as fourniture__entite, f.mode_passation as fourniture__mode_passation, f.porte_appel_offre as fourniture__porte_appel_offre, f.montant as fourniture__montant, f.id_titulaire as fourniture__id_titulaire, f.numero_contrat as fourniture__numero_contrat, f.date_contrat as fourniture__date_contrat, f.date_ordre as fourniture__date_ordre, f.resultat as fourniture__resultat, f.raison_resiliation as fourniture__raison_resiliation, f.ingenieur_reception_provisoire as fourniture__ingenieur_reception_provisoire, f.ingenieur_reception_definitive as fourniture__ingenieur_reception_definitive, f.date_reception_provisoire as fourniture__date_reception_provisoire, f.date_reception_definitive as fourniture__date_reception_definitive, f.id_infrastructure as fourniture__id_infrastructure, f.bailleur as fourniture__bailleur, et.id as etude__id, et.id_infrastructure as etude__id_infrastructure, et.objet_contrat as etude__objet_contrat, et.consistance_contrat as etude__consistance_contrat, et.entite as etude__entite, et.id_titulaire as etude__id_titulaire, et.montant_contrat as etude__montant_contrat, et.numero_contrat as etude__numero_contrat, et.mode_passation as etude__mode_passation, et.porte_appel_offre as etude__porte_appel_offre, et.date_contrat as etude__date_contrat, et.date_ordre_service as etude__date_ordre_service, et.resultat_prestation as etude__resultat_prestation, et.motif_rupture_contrat as etude__motif_rupture_contrat, et.date_information as etude__date_information, et.source_information as etude__source_information, et.mode_acquisition_information as etude__mode_acquisition_information, et.precision_consistance_contrat as etude__precision_consistance_contrat, et.bailleur as etude__bailleur, et.precision_passation as etude__precision_passation  FROM t_ro_01_infrastructure as infraroute LEFT JOIN t_ro_02_situation as s ON infraroute.gid = s.id_infrastructure  LEFT JOIN t_ro_04_surface as srfc ON infraroute.gid = srfc.id_infrastructure  LEFT JOIN t_ro_05_structure as str ON infraroute.gid = str.id_infrastructure LEFT JOIN t_ro_13_foncier as fonc ON infraroute.gid = fonc.id_infrastructure LEFT JOIN t_ro_09_travaux as trav ON infraroute.gid = trav.id_infrastructure LEFT JOIN t_ro_14_fourniture as f ON infraroute.gid = f.id_infrastructure LEFT JOIN t_ro_11_etudes as et ON infraroute.gid = et.id_infrastructure where infraroute.gid = '.$infraId.'';
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    
    public function getAccotementRoute($idRoute)
    {
       $sql = "SELECT accote.cote as accotement__cote, accote.revetue_degradation_de_la_surface as accotement__revetue_degradation_de_la_surface_accote, accote.revetue_dentelle_de_rive as accotement__revetue_dentelle_de_rive, accote.revetue_denivellation_entre_chaussée_et_accotement as accotement__revetue_denivellation_entre_chaussée_et_accotement, accote.revetue_destruction_par_affouillement_de_accotement as accotement__revetue_destruction_par_affouillement_de_accotement, accote.non_revetue_deformation_du_profil as accotement__non_revetue_deformation_du_profil, accote.revetu as accotement__revetu, accote.type as accotement__type, accote.precision_type as accotement__precision_type, accote.id_infrastructure as accotement__id_infrastructure, accote.date_information as accotement__date_information, accote.source_information as accotement__source_information, accote.mode_acquisition_information as accotement__mode_acquisition_information  FROM t_ro_07_accotement as accote WHERE accote.id_infrastructure = ".intval($idRoute)."";
      
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $result = $query->execute();

        return $result->fetchAll();
    }

    public function getFosseRoute($idRoute)
    {
       $sql = "SELECT fosse.cote as fosse__cote, fosse.revetue_degradation_du_fosse as fosse__revetue_degradation_du_fosse, fosse.revetue_section_bouche as fosse__revetue_section_bouche, fosse.non_revetue_profil as fosse__non_revetue_profil, fosse.non_revetue_encombrement as fosse__non_revetue_encombrement, fosse.revetu as fosse__revetu, fosse.id_infrastructure as fosse__id_infrastructure, fosse.date_information as fosse__date_information, fosse.source_information as fosse__source_information, fosse.mode_acquisition_information as fosse__mode_acquisition_information   FROM t_ro_08_fosse as fosse WHERE fosse.id_infrastructure = ".intval($idRoute)."";
      
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
        $sql = "INSERT into t_ro_03_etat (id_infrastructure, etat, date_information, source_Information, mode_acquisition_information) VALUES (".intval($idInfrastructure).", '".$etat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function cleanTablesByIdInfrastructure($idInfrastructure = null, $type = null)
    {
        if (null != $idInfrastructure && $type != null) {

            $table = 't_ro_01_infrastructure';
            $colonne = "gid";
            $selectedcolonne = "gid";
            switch ($type) {
                case 'situation':
                    $table = "t_ro_02_situation";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'surface':
                    $table = "t_ro_04_surface";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'structure':
                    $table = "t_ro_05_structure";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'etat':
                    $table = "t_ro_03_etat";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'accotement':
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
                    break;
                case 'travaux':
                    $table = "t_ro_09_travaux";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'fourniture':
                    $table = "t_ro_14_fourniture";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                case 'etude':
                    $table = "t_ro_11_etudes";
                    $colonne = "id_infrastructure";
                    $selectedcolonne = "id";
                    break;
                default:
                    $table = 't_ro_01_infrastructure';
                    $colonne = "gid";
                    $selectedcolonne = "gid";
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

    public function addInfrastructureRouteSituation($idInfrastructure = null, $fonctionnel = null, $raison, $sourceInformation = null, $modeAcquisitionInformation = null, $etat = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_02_situation (id_infrastructure, fonctionnel, raison, date_information, source_Information, mode_acquisition_information, etat) VALUES (".intval($idInfrastructure).", '".$fonctionnel."', '".$raison."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."', '".$etat."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteSurface($idInfrastructure = null, $revetement = null, $revetueNidDePoule = null, $revetueArrachement = null, $revetueRessuage = null, $revetueFissureLogitudinaleDeJoint = null, $nonRevetueTraverse = null, $nonRevetueBourbier = null, $nonRevetueTeteDeChat = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_04_surface (id_infrastructure, revetement, revetue_nid_de_poule, revetue_arrachement, revetue_ressuage, revetue_fissure_logitudinale_de_joint, non_revetue_traverse, non_revetue_bourbier, non_revetue_tete_de_chat, date_information, source_Information, mode_acquisition_infromation) VALUES (".intval($idInfrastructure).", '".$revetement."', '".$revetueNidDePoule."', '".$revetueArrachement."', '".$revetueRessuage."', '".$revetueFissureLogitudinaleDeJoint."', '".$nonRevetueTraverse."', '".$nonRevetueBourbier."', '".$nonRevetueTeteDeChat."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteStructure($idInfrastructure = null, $revetueDefomation = null, $revetueFissuration = null, $revetueFaiencage = null, $nonRevetueNidsDpoule = null, $nonRevetueDeformation = null,  $nonRevetueToleOndule = null,$nonRevetueRavines = null,  $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
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
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);

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
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);

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
    }

    public function addInfrastructureRouteTravaux($idInfrastructure = null, $objet = null, $consistanceTravaux = null, $modeRealisationTravaux = null, $maitreOuvrage = null, $maitreOuvrageDelegue = null, $maitreOeuvre = null, $idControleSurveillance = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $numeroContrat = null, $dateContrat = null, $dateOrdreService = null, $idTitulaire = null, $resultatTravaux = null, $motifRuptureContrat = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null, $precisionConsistance = null, $modeRealisation = null, $bailleurTravaux = null, $precisionPassation = null)
    {   

        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        $sql = "INSERT into t_ro_09_travaux (id_infrastructure, objet, consistance_travaux, mode_realisation_travaux, maitre_ouvrage, maitre_ouvrage_delegue, maitre_oeuvre, id_controle_surveillance, mode_passation, porte_appel_offre, montant, numero_contrat, date_contrat, date_ordre_service, id_titulaire, resultat_travaux, motif_rupture_contrat, date_reception_provisoire, date_reception_definitive, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_information, source_information, mode_acquisition_information, precision_consistance, mode_realisation, bailleur, precision_passation ) VALUES (".intval($idInfrastructure).", '".$objet."', '".$consistanceTravaux."', '".$modeRealisationTravaux."', '".$maitreOuvrage."', '".$maitreOuvrageDelegue."', '".$maitreOeuvre."', ".intval($idControleSurveillance).", '".$modePassation."', '".$porteAppelOffre."', ".intval($montant).", '".$numeroContrat."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdreService->format("Y-m-d")."', ".intval($idTitulaire).", '".$resultatTravaux."', '".$motifRuptureContrat."','".$dateReceptionProvisoire->format("Y-m-d")."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$precisionConsistance."', '".$modeRealisation."', '".$bailleurTravaux."', '".$precisionPassation."')";
     
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteFourniture($objetContrat = null, $consistanceContrat = null, $materiels = null, $entite = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $idTitulaire = null, $numeroContrat = null, $dateContrat = null, $dateOrdre = null, $resultat = null, $raisonResiliation = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $idInfrastructure = null, $bailleur = null)
    {   
        $sql = "INSERT into t_ro_14_fourniture (objet_contrat, consistance_contrat, materiels, entite, mode_passation, porte_appel_offre, montant, id_titulaire, numero_contrat, date_contrat, date_ordre, resultat, raison_resiliation, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_reception_provisoire, date_reception_definitive, id_infrastructure, bailleur) VALUES ('".$objetContrat."', '".$consistanceContrat."', '".$materiels."', '".$entite."', '".$modePassation."', '".$porteAppelOffre."', ".intval($montant).", ".intval($idTitulaire).", '".$numeroContrat."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdre->format("Y-m-d")."', '".$resultat."', '".$raisonResiliation."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateReceptionProvisoire->format("Y-m-d")."', '".$dateReceptionDefinitive->format("Y-m-d")."', '".intval($idInfrastructure)."', '".$bailleur."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteEtudes($idInfrastructure = null, $objetContrat = null, $consistanceContrat = null, $entite = null, $idTitulaire = null, $montantContrat = null, $numeroContrat = null, $modePassation = null, $porteAppelOffre = null, $dateContrat = null, $dateOrdreService = null, $resultatPrestation = null, $motifRuptureContrat = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null, $precisionConsistanceContrat = null, $bailleur = null, $precisionPassation = null)
    {   
        $sourceInformation = pg_escape_string($sourceInformation);
        $modeAcquisitionInformation = pg_escape_string($modeAcquisitionInformation);
        
        $sql = "INSERT into t_ro_11_etudes (id_infrastructure, objet_contrat, consistance_contrat, entite, id_titulaire, montant_contrat, numero_contrat, mode_passation, porte_appel_offre, date_contrat, date_ordre_service, resultat_prestation, motif_rupture_contrat, date_information, source_information, mode_acquisition_information, precision_consistance_contrat, bailleur, precision_passation) VALUES (".intval($idInfrastructure).", '".$objetContrat."', '".$consistanceContrat."', '".$entite."', ".intval($idTitulaire).", ".intval($montantContrat).", '".$numeroContrat."', '".$modePassation."', '".$porteAppelOffre."', '".$dateContrat->format("Y-m-d")."', '".$dateOrdreService->format("Y-m-d")."', '".$resultatPrestation."', '".$motifRuptureContrat."', '".$dateInformation->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$precisionConsistanceContrat."', '".$bailleur."', '".$precisionPassation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }


    public function getAllyRouteInfo()
    {
        $sql = "select route.gid as id, ST_ASGeoJSON(route.geom) AS geom, route.nom as nom, route.Num as numero, , route.Anc_Nom as cnc_Nom, route.Classe as classe, route.Nom2020 as nom2020 from y_liste_route as route";

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
        $sql = "SELECT c.nom_commun as nom_id_infrastructure, objet_contrat, consistance_contrat, entite, id_titulaire, montant_contrat, numero_contrat, mode_passation, porte_appel_offre, date_contrat, date_ordre_service, resultat_prestation, motif_rupture_contrat, date_information, source_information, mode_acquisition_information, precision_consistance_contrat, bailleurcommune, r.nomreg as nom_region, r.gid as region_id, c.nom_distri as nom_district  FROM limite_communes_mada as c";
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