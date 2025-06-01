<?php
// Vérifier si des données ont été soumises via la méthode GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Récupérer les valeurs des champs de recherche
    $agent_name = $_GET["agent_name"];
    $property_number = $_GET["property_number"];
    $city_name = $_GET["city_name"];

    // Effectuer les traitements appropriés selon les critères de recherche
    if (!empty($agent_name)) {
        // Recherche d'un agent par son nom
        // Exécuter la requête SQL correspondante
    } elseif (!empty($property_number)) {
        // Recherche d'une propriété par son numéro
        // Exécuter la requête SQL correspondante
    } elseif (!empty($city_name)) {
        // Recherche de tous les biens immobiliers par le nom de la ville
        // Exécuter la requête SQL correspondante
    } else {
        // Afficher un message d'erreur si aucun critère de recherche n'a été spécifié
        echo "Veuillez spécifier au moins un critère de recherche.";
    }
}
?>
