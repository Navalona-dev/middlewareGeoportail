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
        $sql = "INSERT into t_ro_01_infrastructure (pk_debut, rattache, categorie, localite,  commune_terrain, gestionnaire, mode_gestion, date_information, source_Information, mode_acquisition_infromation, pk_fin, \"Largeur_chaussée\", \"Largeur_accotements\", \"Structure\", region, district, geom, photo1, photo2, photo3, precision_structure, precision_mode_gestion, photo_name1, photo_name2, photo_name3) VALUES ('".$pkDebut."', '".$rattache."', '".$categorie."', '".$localite."', '".$communeTerrain."', '".$gestionnaire."', '".$modeGestion."', '".$dateInfo->format("Y-m-d")."', '".$sourceInformation."', '".$modeAcquisitionInformation."', '".$pkFin."', '".$largeurHausse."', '".$largeurAccotement."', '".$structure."', '".$region."', '".$district."', ST_GeomFromText('POINT(" . $longitude . " " . $latitude . ")', 4326), '".$photo1."', '".$photo2."', '".$photo3."', '".$precisionStructure."', '".$precisionModeGestion."', '".$photo_name1."', '".$photo_name2."', '".$photo_name3."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }
    
    public function getAllInfrastructuresRoute()
    {
        $sql = 'SELECT infraroute.gid, infraroute.pk_debut, infraroute.rattache, infraroute.categorie, infraroute.localite, infraroute.commune_terrain, infraroute.gestionnaire, infraroute.mode_gestion, infraroute.date_information, infraroute.source_information, infraroute.mode_acquisition_infromation, infraroute.pk_fin, infraroute."Largeur_chaussée", infraroute."Largeur_accotements", infraroute.region, infraroute.district,  ST_X(infraroute.geom) AS long, ST_Y(infraroute.geom) AS lat, infraroute.photo_name1, infraroute.photo_name2, infraroute.photo_name3, e.etat as etat, e.date_information as etat_date_information, e.source_Information as etat_source_Information, e.mode_acquisition_information as etat_mode_acquisition_information, s.fonctionnel as situation_fonctionnel, s.raison, s.date_information as situation_data_info, s.source_Information as situation_src_info, s.mode_acquisition_information as situation_mode_aquis_info, srfc.revetement as surface_revetement, srfc.revetue_nid_de_poule as surface_revetue_nid_de_poule, srfc.revetue_arrachement as surface_revetue_arrachement, srfc.revetue_ressuage as surface_revetue_ressuage, srfc.revetue_fissure_logitudinale_de_joint as surface_fissure_logitudinale_de_joint, srfc.non_revetue_traverse as surface_non_revetue_traverse, srfc.non_revetue_bourbier as surface_non_revetue_bourbier, srfc.non_revetue_tete_de_chat as surface_non_revetue_tete_de_chat, srfc.date_information as surface_date_information, srfc.source_Information as surface_source_Information, srfc.mode_acquisition_infromation as surface_mode_acquisition_infromation, str.revetue_defomation as structure_revetue_defomation, str.revetue_fissuration as structure_revetue_fissuration, str.revetue_faiencage as structure_revetue_faiencage, str.non_revetue_nids_de_poule as structure_non_revetue_nids_de_poule, str.non_revetue_deformation as structure_non_revetue_deformation, str.non_revetue_tole_ondule as structure_non_revetue_tole_ondule, str.non_revetue_ravines as structure_non_revetue_ravines, str.date_information as structure_date_information, str.source_Information as structure_source_Information,  str.mode_acquisition_information as structure_mode_acquisition_information, fonc."Statut" as statut_foncier, fonc.numero_de_reference as numero_de_reference_foncier, fonc.nom_proprietaire as nom_proprietaire_foncier, trav.objet as travaux_objet, trav.consistance_travaux as travaux_consistance_travaux, trav.mode_realisation_travaux as travaux_mode_realisation_travaux, trav.maitre_ouvrage as travaux_maitre_ouvrage, trav.maitre_ouvrage_delegue as travaux_maitre_ouvrage_delegue, trav.maitre_oeuvre as travaux_maitre_oeuvre, trav.id_controle_surveillance as travaux_id_controle_surveillance, trav.mode_passation as travaux_mode_passation, trav.porte_appel_offre as travaux_porte_appel_offre, trav.montant as travaux_montant, trav.numero_contrat as travaux_numero_contrat, trav.date_contrat as travaux_date_contrat, trav.date_ordre_service as travaux_date_ordre_service, trav.id_titulaire as travaux_id_titulaire, trav.resultat_travaux as travaux_resultat_travaux, trav.motif_rupture_contrat as travaux_motif_rupture_contrat, trav.date_reception_provisoire as travaux_date_reception_provisoire, trav.date_reception_definitive as travaux_date_reception_definitive, trav.ingenieur_reception_provisoire as travaux_ingenieur_reception_provisoire, trav.ingenieur_reception_definitive as travaux_ingenieur_reception_definitive, trav.date_information as travaux_date_information, trav.source_information as travaux_source_information, trav.mode_acquisition_information as travaux_mode_acquisition_information, f.objet_contrat as fourniture_objet_contrat, f.consistance_contrat as fourniture_consistance_contrat, f.materiels as fourniture_materiels, f.entite as fourniture_entite, f.mode_passation as fourniture_mode_passation, f.porte_appel_offre as fourniture_porte_appel_offre, f.montant as fourniture_montant, f.id_titulaire as fourniture_id_titulaire, f.numero_contrat as fourniture_numero_contrat, f.date_contrat as fourniture_date_contrat, f.date_ordre as fourniture_date_ordre, f.resultat as fourniture_resultat, f.raison_resiliation as fourniture_raison_resiliation, f.ingenieur_reception_provisoire as fourniture_ingenieur_reception_provisoire, f.ingenieur_reception_definitive as fourniture_ingenieur_reception_definitive, f.date_reception_provisoire as fourniture_date_reception_provisoire, f.date_reception_definitive as fourniture_date_reception_definitive, et.objet_contrat as etude_objet_contrat, et.consistance_contrat as etude_consistance_contrat, et.entite as etude_entite, et.id_titulaire as etude_id_titulaire, et.montant_contrat as etude_montant_contrat, et.numero_contrat as etude_numero_contrat, et.mode_passation as etude_mode_passation, et.porte_appel_offre as etude_porte_appel_offre, et.date_contrat as etude_date_contrat, et.date_ordre_service as etude_date_ordre_service, et.resultat_prestation as etude_resultat_prestation, et.motif_rupture_contrat as etude_motif_rupture_contrat, et.date_information as etude_date_information, et.source_information as etude_source_information, et.mode_acquisition_information as etude_mode_acquisition_information  FROM t_ro_01_infrastructure as infraroute LEFT JOIN t_ro_03_etat as e ON infraroute.gid = e.id_infrastructure LEFT JOIN t_ro_02_situation as s ON infraroute.gid = s.id_infrastructure  LEFT JOIN t_ro_04_surface as srfc ON infraroute.gid = srfc.id_infrastructure  LEFT JOIN t_ro_05_structure as str ON infraroute.gid = str.id_infrastructure LEFT JOIN t_ro_13_foncier as fonc ON infraroute.gid = fonc.id_infrastructure LEFT JOIN t_ro_09_travaux as trav ON infraroute.gid = trav.id_infrastructure LEFT JOIN t_ro_14_fourniture as f ON infraroute.gid = f.id_infrastructure LEFT JOIN t_ro_11_etudes as et ON infraroute.gid = et.id_infrastructure';

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

    public function addInfrastructureRouteSituation($idInfrastructure = null, $fonctionnel = null, $raison, $sourceInformation = null, $modeAcquisitionInformation = null, $etat = null)
    {   
        $sourceInfo = pg_escape_string($sourceInformation);
        $dateInfo = new \DateTime();
        $sql = "INSERT into t_ro_02_situation (id_infrastructure, fonctionnel, raison, date_information, source_Information, mode_acquisition_information, etat) VALUES (".$idInfrastructure.", '".$fonctionnel."', '".$raison."', '".$dateInfo->format("Y-m-d")."', '".$sourceInfo."', '".$modeAcquisitionInformation."', '".$etat."')";
        
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

    public function addInfrastructureRouteFoncier($statut = null, $numeroReference = null, $nomProprietaire = null, $idInfrastructure = null)
    {   
        $sql = "INSERT into t_ro_13_foncier (\"Statut\", cote, numero_de_reference, nom_proprietaire, id_infrastructure) VALUES (".$statut.", '".$numeroReference."', '".$nomProprietaire."', '".$idInfrastructure."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteTravaux($idInfrastructure = null, $objet = null, $consistanceTravaux = null, $modeRealisationTravaux = null, $maitreOuvrage = null, $maitreOuvrageDelegue = null, $maitreOeuvre = null, $idControleSurveillance = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $numeroContrat = null, $dateContrat = null, $dateOrdreService = null, $idTitulaire = null, $resultatTravaux = null, $motifRuptureContrat = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sql = "INSERT into t_ro_09_travaux (id_infrastructure, objet, consistance_travaux, mode_realisation_travaux, maitre_ouvrage, maitre_ouvrage_delegue, maitre_oeuvre, id_controle_surveillance, mode_passation, porte_appel_offre, montant, numero_contrat, date_contrat, date_ordre_service, id_titulaire, resultat_travaux, motif_rupture_contrat, date_reception_provisoire, date_reception_definitive, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_information, source_information, mode_acquisition_information) VALUES (".$idInfrastructure.", '".$objet."', '".$consistanceTravaux."', '".$modeRealisationTravaux."', '".$maitreOuvrage."', '".$maitreOuvrageDelegue."', '".$maitreOeuvre."', '".$idControleSurveillance."', '".$modePassation."', '".$porteAppelOffre."', '".$montant."', '".$numeroContrat."', '".$dateContrat."', '".$dateOrdreService."', '".$idTitulaire."', '".$resultatTravaux."', '".$motifRuptureContrat."', '".$dateReceptionProvisoire."', '".$dateReceptionDefinitive."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateInformation."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteFourniture($objetContrat = null, $consistanceContrat = null, $materiels = null, $entite = null, $modePassation = null, $porteAppelOffre = null, $montant = null, $idTitulaire = null, $numeroContrat = null, $dateContrat = null, $dateOrdre = null, $resultat = null, $raisonResiliation = null, $ingenieurReceptionProvisoire = null, $ingenieurReceptionDefinitive = null, $dateReceptionProvisoire = null, $dateReceptionDefinitive = null, $idInfrastructure = null)
    {   
        $sql = "INSERT into t_ro_14_fourniture (objet_contrat, consistance_contrat, materiels, entite, mode_passation, porte_appel_offre, montant, id_titulaire, numero_contrat, date_contrat, date_ordre, resultat, raison_resiliation, ingenieur_reception_provisoire, ingenieur_reception_definitive, date_reception_provisoire, date_reception_definitive, id_infrastructure) VALUES (".$objetContrat.", '".$consistanceContrat."', '".$materiels."', '".$entite."', '".$modePassation."', '".$porteAppelOffre."', '".$montant."', '".$idTitulaire."', '".$numeroContrat."', '".$dateContrat."', '".$dateOrdre."', '".$resultat."', '".$raisonResiliation."', '".$ingenieurReceptionProvisoire."', '".$ingenieurReceptionDefinitive."', '".$dateReceptionProvisoire."', '".$dateReceptionDefinitive."', '".$idInfrastructure."')";
        
        $conn = $this->entityManager->getConnection();
        $query = $conn->prepare($sql);
        $query->execute();
        $id = $conn->lastInsertId();

        return $id;
    }

    public function addInfrastructureRouteEtudes($idInfrastructure = null, $objetContrat = null, $consistanceContrat = null, $entite = null, $idTitulaire = null, $montantContrat = null, $numeroContrat = null, $modePassation = null, $porteAppelOffre = null, $dateContrat = null, $dateOrdreService = null, $resultatPrestation = null, $motifRuptureContrat = null, $dateInformation = null, $sourceInformation = null, $modeAcquisitionInformation = null)
    {   
        $sql = "INSERT into t_ro_11_etudes (id_infrastructure, objet_contrat, consistance_contrat, entite, id_titulaire, montant_contrat, numero_contrat, mode_passation, porte_appel_offre, date_contrat, date_ordre_service, resultat_prestation, motif_rupture_contrat, date_information, source_information, mode_acquisition_information) VALUES (".$idInfrastructure.", '".$objetContrat."', '".$consistanceContrat."', '".$entite."', '".$idTitulaire."', '".$montantContrat."', '".$numeroContrat."', '".$modePassation."', '".$porteAppelOffre."', '".$dateContrat."', '".$dateOrdreService."', '".$resultatPrestation."', '".$motifRuptureContrat."', '".$dateInformation."', '".$sourceInformation."', '".$modeAcquisitionInformation."')";
        
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