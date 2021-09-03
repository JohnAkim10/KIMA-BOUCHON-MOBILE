<?php 
	include 'fonctions.php';

	$action = $_GET['action'];

	switch ($action) {
		case 'login':
			$email = $_POST['email'];
			$mdp = $_POST['password'];

			echo json_encode(Login($email, $mdp));
			connect($email, $mdp);
			break;
		case 'enregistrer' : 
			$nom = $_POST['nom'];
			$prenom = $_POST['prenom'];
			$image = $_POST['image'];
			$imageData = $_POST['imagedata'];
			$numero = $_POST['telephone'];
			$email = $_POST['email'];
			$mdp = $_POST['password'];

			echo json_encode(enregistrer($nom, $prenom, $image, $imageData, $numero, $email, $mdp));
			break;
		case 'modifierUser' : 
			$id = $_POST['id'];
			$nom = $_POST['nom'];
			$prenom = $_POST['prenom'];
			$numero = $_POST['telephone'];
			$email = $_POST['email'];

			echo json_encode(modifierUser($id, $nom, $prenom, $numero, $email));
			break;
		case 'sendMessage' : 
			$id = $_POST['id'];
			$usermail = $_POST['usermail'];
			$username = $_POST['username'];
			$usernum = $_POST['usernum'];
			$message = $_POST['message'];

			echo json_encode(sendMessage($id, $message));

			sendMail($usermail, $username, $usernum, $message);
			break;
		case 'updateImage' : 
			$iduser = $_POST['iduser'];
			$imageName = $_POST['imagename'];
			$imageData = $_POST['imagedata'];

			echo (updateImage($iduser, $imageName, $imageData));
			break;
		case 'activer' : 
			$id = $_GET['id'];

			echo json_encode(activer($id));
			break;
		case 'desactiver' : 
			$id = $_GET['id'];

			echo json_encode(desactiver($id));
			break;
		case 'postImage' : 
			$imageName = $_POST['imageName'];
			$imageData = $_POST['imageData'];
			$niveau = $_POST['niveau'];
			$description = "description";
			$video = $_POST['videoName'];
			$idcarrefour = $_POST['idcarrefour'];
			$idtroncon = $_POST['idtroncon'];

			echo json_encode(postImage($imageName, $video, $niveau, $description, $idcarrefour, $idtroncon, $imageData));
			break;
		case 'addSituation' : 
			$niveau = $_POST['niveau'];
			$idcarrefour = $_POST['idcarrefour'];
			$idtroncon = $_POST['idtroncon'];

			echo (addSituation($niveau, $idcarrefour, $idtroncon));
			break;
		case 'uploadVideo' :

			$videoName = $_GET['videoName'];
			// $file_name = $_FILES['myFile']['name'];

			$file_size = $_FILES['myFile']['size'];
			$file_type = $_FILES['myFile']['type'];
			$temp_name = $_FILES['myFile']['tmp_name'];

			$location = "videos/";

			move_uploaded_file($temp_name, $location.$videoName);

			// if(updateVideoName($file_name, $imageName)){
			// 	echo "Vidéo uploadé avec succès : ".$imageName;
			// }else{
			// 	echo "Mis à jour du nom écoué  : ".$imageName;
			// }

			echo "Vidéo uploadé avec succès : ".$videoName;

			
			// Baréé à Cimetier1619880394939.jpg
			// echo "Vidéo uploadé avec succès ".$imageName;
			
			break;

		case 'addCarrefour' : 
			$nom = $_POST['nom'];
			$commune = $_POST['commune'];
			$reperes = $_POST['reperes'];
			$description = $_POST['description'];
			$lattitude = $_POST['lattitude'];
			$longitude = $_POST['longitude'];

			echo json_encode(addCarrefour($nom, $commune, $reperes, $description, $lattitude, $longitude));
			break;

		case 'addTroncon' : 
			$idfrom = $_POST['idfrom'];
			$idto = $_POST['idto'];

			echo json_encode(addTroncon($idfrom, $idto));
			break;

		case 'getCarrefours' :
			echo json_encode(["Carrefours" => getCarrefours()]);
			break;

		case 'getTroncons' :
			echo json_encode(["Troncons" => getTroncons($_GET['idcarrefour'])]);
			break;

		case 'getAllTroncons' :
			echo json_encode(["Troncons" => getAllTroncons()]);
			break;

		case 'getCirculations' :
			echo json_encode(["Circulations" => getCirculations($_GET["idtroncon"])]);
			break;
		case 'getSituations' :
			echo json_encode(["Situations" => getSituations()]);
			break;

		case 'getAbonnements' :
			echo json_encode(["Abonnements" => getAbonnements()]);
			break;

		case 'getAbonnement' :
			echo json_encode(getAbonnement($_GET['id']));
			break;

		case 'getUsers' :
			echo json_encode(["Users" => getUsers()]);
			break;

		case 'rechercher' :
			echo json_encode(["Situations" => searchCarrefour($_POST["recherche"])]);
			break;
		case 'searchAbonnement' :
			echo json_encode(["Abonnements" => searchAbonnement($_POST["recherche"])]);
			break;
		case 'getStats' :
			echo json_encode(getStats());
			break;
		case 'deconnexion' :
			$id = $_GET['iduser'];
			echo deconnexion($id);
			break;

		case 'supprimerCirculation' :
			$id = $_POST['id'];
			$image = $_POST['image'];
			$video = $_POST['video'];
			echo supprimerCirculation($id);
			break;

		case 'addAbonnement' :
			$iduser = $_POST['iduser'];
			$type = $_POST['type'];
			$code = $_POST['code'];
			echo addAbonnement($iduser, $type, $code);
			break;

		case 'verifierabonnement' :
			$id = $_GET['iduser'];
			echo verifierabonnement($id);
			break;
		case 'verifierabonnementExist' :
			$id = $_GET['iduser'];
			echo verifierabonnementExist($id);
			break;

		default:
			echo "Cet action n'est pas définie !";
			break;
	}

 ?>