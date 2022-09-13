<?php 

	 $dbhost = 'localhost';
	$dbname = 'KINEXPRESS';
	 $dbuser = 'root';
	$dbpswd = '';



	try{
		$db = new PDO('mysql:host='.$dbhost.';dbname='.$dbname,$dbuser,$dbpswd,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	}catch(PDOexception $e){
		die("Erreur : ".$e->getMessage());
	}

	function GetFromBD($sql){
    global $db;

    $req = $db->query($sql);

    $resultat = array();

    while ($row = $req->fetchObject()) {
      $resultat[] = $row;
    }
    return $resultat;
  }
  function AddIntoBD($elements, $sql){
    global $db;

    $req = $db->prepare($sql);

    return $req->execute($elements);
  }
	

  function getUsers(){
  	$sql = "
        SELECT *
        FROM user
        ORDER BY id DESC
        ";
    return GetFromBD($sql);
  }
  function Login($email, $mdp){
    $mdp = sha1($mdp);
    $sql = "
        SELECT *
        FROM user
        WHERE email = '$email' AND mdp = '$mdp'
        ";
    return GetFromBD($sql)[0];
  }

  function enregistrer($nom, $prenom, $image, $imageData, $numero, $email, $mdp){
    $save = $mdp;
    $mdp = sha1($mdp);
  	$elements = array(
  		"nom" => $nom,
  		"prenom" => $prenom,
  		"image" => $image,
  		"numero" => $numero,
  		"email" => $email,
  		"mdp" => $mdp
  	);
  	$sql = "INSERT INTO user (id, nom, prenom, image, numero, email, isconnected, mdp) VALUES (null, :nom, :prenom, :image, :numero, :email, 1, :mdp)";

  	if(AddIntoBD($elements, $sql)){
  		file_put_contents("Images/Profile/".$image, base64_decode($imageData));
      return Login($email, $save);
    }
  }
  function modifierUser($id,$nom, $prenom, $numero, $email){
    $elements = array(
      "id" => $id,
      "nom" => $nom,
      "prenom" => $prenom,
      "numero" => $numero,
      "email" => $email
    );
    $sql = "UPDATE user SET nom = :nom, prenom = :prenom, numero = :numero, email = :email, isconnected = 1 WHERE id = :id";

    // echo $id;
    return AddIntoBD($elements, $sql);
    
  }
  function connect($email, $mdp){

    $elements = array(
      "email" => $email,
      "mdp" => $mdp
    );

    $sql = "UPDATE user SET isconnected = 1 WHERE email = :email AND mdp = :mdp";

    return AddIntoBD($elements, $sql);
    
  }
  function updateVideoName($video, $image){

    $elements = array(
      "video" => $video,
      "image" => $image
    );

    $sql = "UPDATE circulation SET video = :video WHERE image = :image";

    return AddIntoBD($elements, $sql);
    
  }
  function updateImage($iduser, $imageName, $imageData){
    $elements = array(
      "id" => $iduser,
      "image" => $imageName
    );
    $sql = "UPDATE user SET image = :image WHERE id = :id";

    file_put_contents("Images/Profile/".$imageName, base64_decode($imageData));

    return AddIntoBD($elements, $sql) ? 1 : 0;
    
  }
  function deconnexion($iduser){
    $elements = array(
      "id" => $iduser
    );
    $sql = "UPDATE user SET isconnected = 0 WHERE id = :id";

    return AddIntoBD($elements, $sql) ? "true" : "false"; 
  }

  function supprimerCirculation($id){
    $elements = array(
      "id" => $id
    );
    $sql = "DELETE FROM circulation WHERE id = :id";

    return AddIntoBD($elements, $sql) ? "true" : "false";
  }

  function getCarrefours(){
    $sql = "
        SELECT 
              *
        FROM carrefour
        ORDER BY nom asc
        ";
    return GetFromBD($sql);
  }
  function getTroncons($idcarrefour){
    $sql = "
        SELECT 
              tr.id,
              tr.idfrom,
              tr.idto,
              (SELECT nom from carrefour AS c WHERE c.id = tr.idfrom) as depuis,
            (SELECT lattitude FROM carrefour WHERE id = tr.idfrom) as latdepuis,
            (SELECT longitude FROM carrefour WHERE id = tr.idfrom) as longdepuis,
              (SELECT nom from carrefour AS c WHERE c.id = tr.idto) as vers,
            (SELECT lattitude FROM carrefour WHERE id = tr.idto) as latvers,
            (SELECT longitude FROM carrefour WHERE id = tr.idto) as longvers
        FROM troncon AS tr
        WHERE idfrom = $idcarrefour OR idto = $idcarrefour
        ";
    return GetFromBD($sql);
  }
  function getAllTroncons(){
    $sql = "
        SELECT 
              tr.id,
              tr.idfrom,
              tr.idto,
              (SELECT nom from carrefour AS c WHERE c.id = tr.idfrom) as depuis,
            (SELECT lattitude FROM carrefour WHERE id = tr.idfrom) as latdepuis,
            (SELECT longitude FROM carrefour WHERE id = tr.idfrom) as longdepuis,
              (SELECT nom from carrefour AS c WHERE c.id = tr.idto) as vers,
            (SELECT lattitude FROM carrefour WHERE id = tr.idto) as latvers,
            (SELECT longitude FROM carrefour WHERE id = tr.idto) as longvers
        FROM troncon AS tr
        ";
    return GetFromBD($sql);
  }

  function getCirculations($idtroncon){
  	$sql = "
	        SELECT 
    				ci.id,
    				ci.image,
    				ci.video,
    				ci.niveau,
    				ci.description,
	         		ci.date,
     				c.nom as carrefour, 
     				c.commune, 
     				c.reperes, 
     				c.description,
            (SELECT nom FROM carrefour WHERE id = tr.idfrom) as depuis,
            (SELECT lattitude FROM carrefour WHERE id = tr.idfrom) as latdepuis,
            (SELECT longitude FROM carrefour WHERE id = tr.idfrom) as longdepuis,
            (SELECT nom FROM carrefour WHERE id = tr.idto) as vers,
            (SELECT lattitude FROM carrefour WHERE id = tr.idto) as latvers,
            (SELECT longitude FROM carrefour WHERE id = tr.idto) as longvers,
            tr.id 
    		FROM circulation as ci
			JOIN carrefour as c
			ON ci.idcarrefour = c.id
      JOIN troncon as tr
      ON ci.idtroncon = tr.id
			WHERE ci.idtroncon = $idtroncon and ci.image != 'null'
			ORDER BY ci.id DESC
      LIMIT 20
        ";
    return GetFromBD($sql);
  }
  function getCirculationsAll($idtroncon){
    $sql = "
          SELECT 
            ci.id,
            ci.image,
            ci.video,
            ci.niveau,
            ci.description,
              ci.date,
            c.nom as carrefour, 
            c.commune, 
            c.reperes, 
            c.description,
            (SELECT nom FROM carrefour WHERE id = tr.idfrom) as depuis,
            (SELECT lattitude FROM carrefour WHERE id = tr.idfrom) as latdepuis,
            (SELECT longitude FROM carrefour WHERE id = tr.idfrom) as longdepuis,
            (SELECT nom FROM carrefour WHERE id = tr.idto) as vers,
            (SELECT lattitude FROM carrefour WHERE id = tr.idto) as latvers,
            (SELECT longitude FROM carrefour WHERE id = tr.idto) as longvers,
            tr.id 
        FROM circulation as ci
      JOIN carrefour as c
      ON ci.idcarrefour = c.id
      JOIN troncon as tr
      ON ci.idtroncon = tr.id
      WHERE ci.idtroncon = $idtroncon
      ORDER BY ci.id DESC
      LIMIT 20
        ";
    return GetFromBD($sql);
  }

  function getSituations(){
  	$sql  = "
		  	SELECT  cir.image,
		  			cir.video,
            cir.niveau,
		  			cir.date,
            (SELECT nom FROM carrefour WHERE id = cir.idcarrefour) as carrefour,
            (SELECT nom FROM carrefour WHERE id = tr.idfrom) as depuis,
            (SELECT nom FROM carrefour WHERE id = tr.idto) as vers,
            (SELECT nom FROM carrefour WHERE id = tr.idfrom) as depuis,
            (SELECT lattitude FROM carrefour WHERE id = tr.idfrom) as latdepuis,
            (SELECT longitude FROM carrefour WHERE id = tr.idfrom) as longdepuis,
            (SELECT nom FROM carrefour WHERE id = tr.idto) as vers,
            (SELECT lattitude FROM carrefour WHERE id = tr.idto) as latvers,
            (SELECT longitude FROM carrefour WHERE id = tr.idto) as longvers,
            tr.id 
			FROM circulation cir
      JOIN troncon tr 
      ON cir.idtroncon = tr.id
			WHERE  cir.image != 'NULL' 
      AND cir.date = 
          (SELECT cir.date d FROM circulation cir WHERE cir.idtroncon = tr.id AND cir.image != 'NULL' ORDER BY d DESC LIMIT 1)
      ORDER BY cir.id DESC
      LIMIT 50
			";
    return GetFromBD($sql);
  }

  // function getSituations(){
  //   $sql  = "
  //       SELECT  cir.image,
  //           cir.video,
  //           cir.niveau,
  //           cir.date,
  //           car.id, 
  //           car.nom as carrefour, 
  //           car.commune, 
  //           car.reperes, 
  //           car.description
  //     FROM carrefour car
  //     JOIN circulation cir
  //     ON car.id = cir.idcarrefour
  //     WHERE cir.date = (SELECT cir.date d FROM circulation cir 
  //     WHERE cir.idcarrefour = car.id ORDER BY d DESC LIMIT 1)
  //     ORDER BY cir.id DESC
  //     LIMIT 50
  //     ";
  //   return GetFromBD($sql);
  // }

  function searchCarrefour($recherche){
    $sql = "
          SELECT  cir.image, cir.video,
            cir.niveau,
            cir.date,
            (SELECT nom FROM carrefour WHERE id = cir.idcarrefour) as carrefour,
            (SELECT nom FROM carrefour WHERE id = tr.idfrom) as depuis,
            (SELECT nom FROM carrefour WHERE id = tr.idto) as vers,
            (SELECT nom FROM carrefour WHERE id = tr.idfrom) as depuis,
            (SELECT lattitude FROM carrefour WHERE id = tr.idfrom) as latdepuis,
            (SELECT longitude FROM carrefour WHERE id = tr.idfrom) as longdepuis,
            (SELECT nom FROM carrefour WHERE id = tr.idto) as vers,
            (SELECT lattitude FROM carrefour WHERE id = tr.idto) as latvers,
            (SELECT longitude FROM carrefour WHERE id = tr.idto) as longvers,
            tr.id 
          FROM carrefour car
          JOIN circulation cir
          ON car.id = cir.idcarrefour
          JOIN troncon tr 
          ON cir.idtroncon = tr.id
          WHERE cir.date = (  
                            SELECT cir.date d 
                            FROM circulation cir 
                            WHERE cir.idcarrefour = car.id 
                            ORDER BY d DESC 
                            LIMIT 1
                            )
          AND (car.nom like '%$recherche%' OR car.commune like '%$recherche%' OR car.reperes like '%$recherche%')

          ORDER BY car.nom ASC
        ";
    return GetFromBD($sql);
  }

  function getAbonnements(){
  	$sql = "
  			SELECT 
  					a.id,
  					a.iduser,
  					a.type,
  					a.debut,
  					a.fin,
  					a.codepaiement as code,
            a.statut,
  					u.nom,
  					u.prenom,
  					u.image,
  					u.numero,
  					u.email
  			FROM abonnement as a 
  			JOIN user as u 
  			ON a.iduser = u.id
        WHERE a.fin > NOW()
        ORDER BY a.codepaiement DESC
        LIMIT 20
  			";
  	return GetFromBD($sql);
  }
  function getAbonnement($id){
    $sql = "
        SELECT 
            a.id,
            a.iduser,
            a.type,
            a.debut,
            a.fin,
            a.codepaiement as code,
            a.statut,
            u.nom,
            u.prenom,
            u.image,
            u.numero,
            u.email
        FROM abonnement as a 
        JOIN user as u 
        ON a.iduser = u.id
        WHERE a.fin > NOW() AND a.iduser = $id
        ORDER BY a.codepaiement DESC
        ";
    return GetFromBD($sql)[0];
  }

  function searchAbonnement($recherche){
    $sql = "
        SELECT 
            a.id,
            a.iduser,
            a.type,
            a.debut,
            a.fin,
            a.codepaiement as code,
            a.statut,
            u.nom,
            u.prenom,
            u.image,
            u.numero,
            u.email
        FROM abonnement as a 
        JOIN user as u 
        ON a.iduser = u.id
        WHERE (u.nom like '%$recherche%' OR u.prenom like '%$recherche%' OR u.image like '%$recherche%' OR u.numero like '%$recherche%' OR u.email like '%$recherche%' OR a.type like '%$recherche%' OR a.statut like '%$recherche%' OR a.codepaiement like '%$recherche%' OR a.debut like '%$recherche%' OR a.fin like '%$recherche%') AND a.fin > NOW()
        ORDER BY a.codepaiement DESC
        ";
    return GetFromBD($sql);
  }

  function sendMessage($iduser, $message){
  	$elements = array(
  		"user" => $iduser,
  		"message" => $message
  	);
  	$sql = "INSERT INTO message (id, iduser, message, date) VALUES (null, :user, :message, NOW())";

  	return AddIntoBD($elements, $sql);
  }

  function addCarrefour($nom, $commune, $reperes, $description, $lattitude, $longitude){
    $elements = array(
      "nom" => $nom,
      "commune" => $commune,
      "reperes" => $reperes,
      "description" => $description,
      "lattitude" => $lattitude,
      "longitude" => $longitude
    );
    $sql = "INSERT INTO carrefour (id, nom, commune, reperes, description, lattitude, longitude) VALUES (null, :nom, :commune, :reperes, :description, :lattitude, :longitude)";

    return AddIntoBD($elements, $sql);
  }

  function addTroncon($idfrom, $idto){
    $elements = array(
      "idfrom" => $idfrom,
      "idto" => $idto
    );
    $sql = "INSERT INTO troncon (idfrom, idto) VALUES (:idfrom, :idto)";

    return AddIntoBD($elements, $sql);
  }

  function addAbonnement($iduser, $type, $code){
    switch ($type) {
      case '1':
        $duree = 14;
        break;
      case '2':
      $duree = 30;
        break;
      case '3':
        $duree = 14;
        break;
      case '4':
        $duree = 30;
        break;
      default:
        $duree = 0;
        break;
    }
    $fin = getDateAfter($duree);
    $elements = array(
      "iduser" => $iduser,
      "type" => $type,
      "fin" => $fin,
      "code" => $code
    );
    $sql = "INSERT INTO abonnement (iduser, type, fin, codepaiement) VALUES (:iduser, :type, :fin, :code)";
      // echo $iduser;
    if (AddIntoBD($elements, $sql)){
      return "true";
    }else{
      return "false";
    }
  }

  function getDateAfter($days){
    $now = date("d/m/Y");

    $aftertimestamp = mktime(0, 0, 0, date("m"), date("d")+$days, date("Y"));
    $date = date("Y/m/d H:m:s",$aftertimestamp);

    return $date;
  }


  function activer($id){
    $elements = array(
      "id" => $id
    );
    $sql = "UPDATE abonnement SET statut = 1 WHERE id = :id";

    return AddIntoBD($elements, $sql);
  }

  function desactiver($id){
    $elements = array(
      "id" => $id
    );
    $sql = "UPDATE abonnement SET statut = 0 WHERE id = :id";

    return AddIntoBD($elements, $sql);
  }

  function postImage($image, $video, $niveau, $description, $idcarrefour, $idtroncon, $imageData){
    $elements = array(
      "image" => $image,
      "video" => $video,
      "niveau" => $niveau,
      "description" => $description,
      "idcarrefour" => $idcarrefour,
      "idtroncon" => $idtroncon
    );

    $sql = "INSERT INTO circulation (id, image, video, niveau, description, idcarrefour, idtroncon, date) VALUES (null, :image, :video, :niveau, :description, :idcarrefour, :idtroncon, NOW())";

    // echo $image;

    file_put_contents("Images/Circulation/".$image, base64_decode($imageData));

    return AddIntoBD($elements, $sql);
  }
  function addSituation($niveau, $idcarrefour, $idtroncon){
    $elements = array(
      "niveau" => $niveau,
      "idcarrefour" => $idcarrefour,
      "idtroncon" => $idtroncon
    );

    $sql = "INSERT INTO circulation (id, niveau, idcarrefour, idtroncon, date) VALUES (null,:niveau, :idcarrefour, :idtroncon, NOW())";
    if (AddIntoBD($elements, $sql)) {
      return "true";
    }else{
      return "false";
    }    
  }

  function getStats(){
    
    $sql = "
              SELECT 
                    COUNT(a.id) as abonnements,
                    (SELECT COUNT(id) FROM circulation) as circulations,
                    (SELECT COUNT(id) FROM carrefour) as carrefours,
                    (SELECT COUNT(id) FROM user as u WHERE u.isconnected = 1) as utilisateurs
              FROM abonnement as a
              WHERE a.statut = 1
            ";

    return GetFromBD($sql)[0];

  }
  function verifierabonnement($iduser){
    $sql = "
              SELECT 
                    a.statut
              FROM abonnement as a
              WHERE a.iduser = $iduser AND a.fin > NOW()
            ";

    $result = GetFromBD($sql);

    $statut = $result[0]->statut;
    if ($statut == 1) {
      return "true";
    }else{
      return "false";
    }
  }
  function verifierabonnementExist($iduser){
    
    $sql = "
              SELECT 
                    *
              FROM abonnement as a
              WHERE a.iduser = $iduser AND a.fin > NOW()
            ";

    $result = GetFromBD($sql);

    if (!empty($result)) {
      $type = $result[0]->type;
      $code = $result[0]->codepaiement;
      echo $type."_".$code;
    }
    return "";
  }
  function entyInject($elem)
  {
    return utf8_decode(trim(htmlspecialchars(strip_tags($elem), ENT_QUOTES)));
  }

  function sendMail($usermail, $username, $usernum, $message){

    $to = "johnkayangi@gmail.com ";
    $from = entyInject($usermail);
    $name = entyInject($username);
    $subject = 'Message d\'un utilisateurs de KINEXPRESS';
    $number = entyInject($usernum);
    $cmessage = 'L\'utilisateurs a écrit : <br/>';
    $cmessage .= $message;

    $headers = "From: $from";
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";



    $logo = 'Images/bouchon.jpg';
    $link = 'http://kinexpress.com';

    $body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Express Mail</title></head><body>";
    $body .= "<table style='width: 100%;'>";
    $body .= "<thead style='text-align: center;'><tr><td style='border:none;' colspan='2'>";
    $body .= "<a href='{$link}'><img src='{$logo}' alt=''></a><br><br>";
    $body .= "</td></tr></thead><tbody><tr>";
    $body .= "<td style='border:none;'><strong>Nom:</strong> {$name}</td>";
    $body .= "<td style='border:none;'><strong>Email:</strong> {$from}</td>";
    $body .= "</tr>";
    $body .= "<tr><td style='border:none;'><strong>Sujet:</strong> {$csubject}</td></tr>";
    $body .= "<tr><td></td></tr>";
    $body .= "<tr><td colspan='2' style='border:none;'>{$cmessage}</td></tr>";
    $body .= "</tbody></table>";
    $body .= "</body></html>";


    if (mail($to, $subject, $body, $headers)) {
        $msg = "Votre devis a été envoyé avec succés.";
        $success = 1;
    } else $msg = "erreur survenu lors de l'envoie du devis";
  }

 ?>
