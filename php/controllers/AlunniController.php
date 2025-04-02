<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args)
  {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $query = "SELECT * FROM alunni";
    $queryParams = $request->getQueryParams();
    if (isset($queryParams['search'])) {
      $query .= " WHERE nome LIKE '%" . $queryParams['search'] . "%' OR cognome LIKE '%" . $queryParams['search'] . "%'";
    }

    if (isset($queryParams['sortCol']) && isset($queryParams['sort'])) {
      $query .= " ORDER BY " . $queryParams['sortCol'] . " " . $queryParams['sort'];
    }

    $result = $mysqli_connection->query($query);
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  public function alunniWithId(Request $request, Response $response, $args)
  {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id=$args[id]");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    if ($mysqli_connection->affected_rows > 0) {

      $response->getBody()->write(json_encode($results));
      return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    } else {
      $response->getBody()->write(json_encode(array("message" => "not found")));
      return $response->withHeader("Content-Type", "application/json")->withStatus(404);
    }
  }

  public function create(Request $request, Response $response, $args)
  {
    $data = json_decode($request->getBody()->getContents(), true);
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if (!isset($data["nome"]) || !isset($data["cognome"])) {
      $response->getBody()->write(json_encode(array("message" => "campi incompleti")));
      return $response->withHeader("Content-Type", "application/json")->withStatus(400);
  }
    $raw_query = "INSERT INTO alunni (nome, cognome) VALUES ('$data[nome]', '$data[cognome]');";
    $result = $mysqli_connection->query($raw_query);
    if ($result && $mysqli_connection->affected_rows > 0) {
      $response->getBody()->write(json_encode(array("message" => "created")));
    } else {
      $response->getBody()->write(json_encode(array("message" => $mysqli_connection->error)));
    }
    return $response->withHeader("Content-Type", "application/json")->withStatus(201);
  }

  public function update(Request $request, Response $response, $args)
  {
    $data = json_decode($request->getBody()->getContents(), true);
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $raw_query = "UPDATE alunni SET";
    $prima_aggiunta = true;
    if (isset($data["nome"])) {
      if (!$prima_aggiunta) {
        $raw_query .= ",";
      }
      $raw_query .= " nome='$data[nome]'";
      $prima_aggiunta = false;
    }
    if (isset($data["cognome"])) {
      if (!$prima_aggiunta) {
        $raw_query .= ",";
      }
      $raw_query .= " cognome='$data[cognome]'";
      $prima_aggiunta = false;
    }
    $raw_query .= " WHERE id=$args[id]";
    $result = $mysqli_connection->query($raw_query);
    if ($mysqli_connection->affected_rows > 0) {
      $response->getBody()->write(json_encode(array("message" => "updated")));
      return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    } else {
      $response->getBody()->write(json_encode(array("message" => "not found")));
      return $response->withHeader("Content-Type", "application/json")->withStatus(404);
    }
  }

  public function destroy(Request $request, Response $response, $args)
  {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $raw_query = "DELETE FROM alunni WHERE id=$args[id]";
    $result = $mysqli_connection->query($raw_query);
    if ($mysqli_connection->affected_rows > 0) {
        $response->getBody()->write(json_encode(array("message" => "deleted")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
      } else {
        $response->getBody()->write(json_encode(array("message" => "not found")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(404);
      }
  }
}