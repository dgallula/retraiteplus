<?php
// Classe pour gérer la connexion à la base de données
class Database {
    private $servername = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $dbname = "maisons_retraite";
    private $conn;

    // Méthode pour établir la connexion à la base de données
    public function connect() {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            echo "Erreur de connexion: " . $e->getMessage();
        }
    }

    // Méthode pour fermer la connexion à la base de données
    public function close() {
        $this->conn = null;
    }
}

// Classe pour gérer les opérations de notation
class Notation {
    private $conn;

    // Constructeur prenant en paramètre une connexion PDO
    function __construct($db) {
        $this->conn = $db;
    }

    // Méthode pour insérer une nouvelle notation dans la base de données
    public function insertNotation($maison_retraite_id, $question_id, $notation) {
        try {
            $sql = "INSERT INTO Notations (maison_retraite_id, question_id, notation) VALUES (:maison_retraite_id, :question_id, :notation)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':maison_retraite_id', $maison_retraite_id);
            $stmt->bindParam(':question_id', $question_id);
            $stmt->bindParam(':notation', $notation);
            $stmt->execute();
            echo "Notation enregistrée avec succès";
        } catch(PDOException $e) {
            echo "Erreur lors de l'enregistrement de la notation: " . $e->getMessage();
        }
    }
}

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();

// Création d'une instance de la classe Notation
$notation = new Notation($conn);

// Récupération des données du formulaire
$maison_retraite_id = $_POST['maison_retraite_id'];
$question_id = $_POST['question_id'];
$notation_value = $_POST['notation'];

// Insertion de la notation dans la base de données
$notation->insertNotation($maison_retraite_id, $question_id, $notation_value);

// Fermeture de la connexion
$db->close();
?>

<?php

// Classe pour représenter une maison de retraite
class MaisonRetraite {
    public $id;
    public $nom;
    public $adresse;
    public $ville;
    public $code_postal;
    public $pays;

    function __construct($id, $nom, $adresse, $ville, $code_postal, $pays) {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->code_postal = $code_postal;
        $this->pays = $pays;
    }

    // Méthode pour générer le formulaire de notation
    public function genererFormulaireNotation() {
        echo '<div class="container">';
        echo '<h2>Notation de la Maison de Retraite ' . $this->nom . '</h2>';
        echo '<form action="traitement.php" method="POST">';
        echo '<input type="hidden" name="maison_retraite_id" value="' . $this->id . '">';

        // Vous pouvez récupérer les questions de la base de données et les afficher ici
        // Pour cet exemple, je vais utiliser des questions statiques
        $questions = array(
            'Globalement, êtes-vous satisfait de cet établissement ?',
            'Conseillerez-vous cet établissement à quelqu\'un ?',
            'Appréciez-vous le moment du repas ?',
            'Estimez-vous que la nourriture servie par l\'établissement est :'
        );

        foreach ($questions as $key => $question) {
            echo '<div class="form-group">';
            echo '<label for="question' . ($key+1) . '">' . $question . '</label>';
            echo '<input id="question' . ($key+1) . '" name="question' . ($key+1) . '" type="number" class="rating" data-max="4" data-min="1" data-step="1">';
            echo '</div>';
        }

        echo '<button type="submit" class="btn btn-primary">Soumettre</button>';
        echo '</form>';
        echo '</div>';
    }
}

// Exemple d'utilisation de la classe MaisonRetraite
$maison_retraite = new MaisonRetraite(1, 'Résidence Les Jardins du Lac', '15 Avenue des Lilas', 'Paris', '75012', 'France');
$maison_retraite->genererFormulaireNotation();
?>

<?php

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$database = "maisons_retraite";

$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupérer les maisons de retraite
$sql = "SELECT * FROM MaisonRetraite";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Parcourir chaque maison de retraite
    while($row = $result->fetch_assoc()) {
        $maison_retraite_id = $row["maison_retraite_id"];
        $nom_maison_retraite = $row["nom"];
        
        // Récupérer les notations pour cette maison de retraite
        $sql_notations = "SELECT * FROM Notations WHERE maison_retraite_id = $maison_retraite_id";
        $result_notations = $conn->query($sql_notations);
        
        // Initialiser un tableau pour stocker les moyennes des notations
        $moyennes = array();
        
        // Parcourir chaque notation pour cette maison de retraite
        while($row_notation = $result_notations->fetch_assoc()) {
            $question_id = $row_notation["question_id"];
            $notation = $row_notation["notation"];
            
            // Ajouter la notation à la somme pour cette question
            if (!isset($moyennes[$question_id])) {
                $moyennes[$question_id] = array('somme' => 0, 'count' => 0);
            }
            $moyennes[$question_id]['somme'] += $notation;
            $moyennes[$question_id]['count']++;
        }
        
        // Afficher les moyennes pour chaque question
        echo "<h2>Moyennes pour la maison de retraite $nom_maison_retraite</h2>";
        foreach ($moyennes as $question_id => $moyenne) {
            $moyenne_question = $moyenne['somme'] / $moyenne['count'];
            echo "Moyenne pour la question $question_id : $moyenne_question<br>";
        }
    }
} else {
    echo "Aucune maison de retraite trouvée.";
}

// Fermer la connexion à la base de données
$conn->close();
?>

