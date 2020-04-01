<?php

define('DB_USER', 'changez moi');
define('DB_PASS', 'changez moi');
define('DB_NAME', 'photo-blog');
define('DB_TABLE', 'customer');

/**
 * Regroupe les fonctionnalités liées au consommateur
 */
class Customer
{
  /**
   * Ouvre la connection à la base de données.
   * 
   * @throws Exception En cas d'erreur de connection.
   */
  public function __construct() {
    try {
      $this->db = new PDO(
        'mysql:dbname='.DB_NAME.';host=127.0.0.1',
        DB_USER,
        DB_PASS
      );
    }
    catch (PDOException $e){
      throw new Exception('Impossible de se connecter à la base de données.');
    }
  }

  /**
   * Ferme la connection à la base de données.
   */
  public function __destruct() {
    $this->db = null;
  }

  /**
   * Vérifie si un consommateur est enregistré en base de données.
   * 
   * @param string $email Adresse email du consommateur.
   * @return boolean Vrai si l'adresse email est enregistré en BDD.
   * @throws Exception En cas d'erreur de lecture.
   */
  public function exists($email) {
    $query = $this->db->prepare(
      'SELECT COUNT(email) as count FROM '.DB_TABLE.' WHERE email=?'
    );
    $done = $query->execute([$email]);
    if (!$done) throw new Exception('Erreur à la lecture de la table des consommateurs.');
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
  }

  /**
   * Enregistre un consommateur dans la base de données.
   * 
   * @param string $email Adresse email du consommateur.
   * @param string $password Mot de passe du consommateur.
   * @throws Exception En cas d'erreur d'écriture.
   */
  public function register($email, $password) {
    $hash  = password_hash($password, PASSWORD_DEFAULT);
    $query = $this->db->prepare(
      'INSERT INTO '.DB_TABLE.' (`email`, `password`) VALUES (?, ?)'
    );
    $done = $query->execute([$email, $hash]);
    if (!$done) throw new Exception('Erreur à l\'écriture dans la table des consommateurs.');
  }

  /**
   * Ouvre une session consommateur.
   * 
   * @param string $email Adresse email du consommateur.
   * @param string $password Mot de passe du consommateur.
   * @return boolean Vrai si la session a été ouverte.
   * @throws Exception En cas d'erreur de lecture.
   * @see https://www.php.net/manual/en/session.security.ini.php
   */
  public function login($email, $password) {
    $query = $this->db->prepare(
      'SELECT password FROM '.DB_TABLE.' WHERE email=?'
    );
    $done = $query->execute([$email]);
    if (!$done) throw new Exception('Erreur à la lecture de la table des consommateurs.');
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (empty($result) // TODO make sure result is empty if no email match
    || !password_verify($password, $result['password'])
    || !session_start([
      'cookie_lifetime' => 0,
      'use_cookies' => 'On',
      'use_only_cookies' => 'On',
      'use_strict_mode' => 'On',
      'cookie_httponly' => 'On',
      'cache_limiter' => 'nocache'
    ])){
      return false;
    }
    $_SESSION['logged'] = true;
    $_SESSION['email']  = $email;
    return true;
  }

  /**
   * Ferme une session consommateur.
   */
  public function logout() {
    unset($_SESSION['logged']);
    unset($_SESSION['email']);
  }


}

?>