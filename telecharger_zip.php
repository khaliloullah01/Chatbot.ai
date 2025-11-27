<?php
// telecharger_zip.php

// Désactiver l'affichage des erreurs pour l'utilisateur final
error_reporting(0);
ini_set('display_errors', 0);

// Vérifier si le paramètre dossier est présent
if (!isset($_GET['dossier']) || empty($_GET['dossier'])) {
    http_response_code(400);
    die('Paramètre dossier manquant');
}

$dossierMatiere = $_GET['dossier'];
$cheminRacine = __DIR__;

// Mapping des dossiers de matières vers leurs chemins
$cheminsMatiere = [
    'algebres_l1' => $cheminRacine . '/cours/algebres_l1/Cours',
    'Algorithmes' => $cheminRacine . '/cours/Algorithmes/Cours',
    'analyse_l1' => $cheminRacine . '/cours/analyse_l1/Cours'
];

// Vérifier si le dossier existe
if (!isset($cheminsMatiere[$dossierMatiere]) || !is_dir($cheminsMatiere[$dossierMatiere])) {
    http_response_code(404);
    die('Dossier matière non trouvé ou dossier "Cours" vide');
}

$cheminCours = $cheminsMatiere[$dossierMatiere];

// Vérifier si l'extension ZipArchive est disponible
if (!class_exists('ZipArchive')) {
    http_response_code(500);
    die('Extension Zip non disponible sur le serveur');
}

// Créer un fichier ZIP temporaire
$zip = new ZipArchive();
$nomFichierZip = tempnam(sys_get_temp_dir(), $dossierMatiere . '_') . '.zip';

if ($zip->open($nomFichierZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    die('Impossible de créer le fichier ZIP');
}

// Fonction pour ajouter les fichiers PDF du dossier Cours
function ajouterFichiersCoursAuZip($dossierCours, $zip, $dossierMatiere) {
    if (!is_dir($dossierCours)) {
        return 0;
    }
    
    $compteurFichiers = 0;
    $fichiers = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dossierCours, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($fichiers as $fichier) {
        $cheminFichier = $fichier->getRealPath();
        
        // Ne prendre que les fichiers (pas les dossiers)
        if (is_file($cheminFichier)) {
            // Ne prendre que les fichiers PDF et les documents de cours
            $extension = strtolower(pathinfo($cheminFichier, PATHINFO_EXTENSION));
            $extensionsAutorisees = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
            
            if (in_array($extension, $extensionsAutorisees)) {
                // Créer le chemin relatif pour le ZIP
                $cheminRelatif = basename($cheminFichier);
                
                // Ajouter le fichier au ZIP
                if ($zip->addFile($cheminFichier, $cheminRelatif)) {
                    $compteurFichiers++;
                }
            }
        }
    }
    
    return $compteurFichiers;
}

// Ajouter les fichiers du dossier Cours au ZIP
$nombreFichiersAjoutes = ajouterFichiersCoursAuZip($cheminCours, $zip, $dossierMatiere);

// Fermer le ZIP
$zip->close();

// Vérifier si le ZIP contient des fichiers
if ($nombreFichiersAjoutes === 0) {
    if (file_exists($nomFichierZip)) {
        unlink($nomFichierZip);
    }
    http_response_code(404);
    die('Aucun fichier PDF trouvé dans le dossier Cours de cette matière');
}

// Envoyer le fichier ZIP au client
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $dossierMatiere . '_cours_complet.zip"');
header('Content-Length: ' . filesize($nomFichierZip));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Transfer-Encoding: binary');

// Lire et envoyer le fichier
readfile($nomFichierZip);

// Supprimer le fichier temporaire
unlink($nomFichierZip);
exit;
?>