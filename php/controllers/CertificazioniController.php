<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CertificazioniController
{
  public function certificazioni_alunno(Request $request, Response $response, $args)
  {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $query = "SELECT * FROM certificazioni
              WHERE";
    $queryParams = $request->getQueryParams();
    if (isset($queryParams['search'])) {
      $query .= " (titolo LIKE '%" . $queryParams['search'] . "%' OR votazione LIKE '%" . $queryParams['search'] . "%' OR ente LIKE '%" . $queryParams['search'] . "%') AND";
    }

    $query .= " alunno_id=$args[id]";

    if (isset($queryParams['sortCol']) && isset($queryParams['sort'])) {
      $query .= " ORDER BY " . $queryParams['sortCol'] . " " . $queryParams['sort'];
    }

    $result = $mysqli_connection->query($query);
    $results = $result->fetch_all(MYSQLI_ASSOC);

    if ($mysqli_connection->affected_rows > 0) {

        $response->getBody()->write(json_encode($results));
        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
      } else {
        $response->getBody()->write(json_encode(array("message" => "not found")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(404);
      }
  }

  public function certificazioneWithId(Request $request, Response $response, $args)
  {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazioni WHERE id=$args[id]");
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
    if (!isset($data["titolo"]) || !isset($data["votazione"]) || !isset($data["ente"])) {
        $response->getBody()->write(json_encode(array("message" => "campi incompleti")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }

    if(($data["votazione"] > 100) || ($data["votazione"] < 0)) {
        $response->getBody()->write(json_encode(array("message" => "campi incorretti")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $raw_query = "INSERT INTO certificazioni (alunno_id, titolo, votazione, ente) VALUES ('$args[id]', '$data[titolo]', '$data[votazione]', '$data[ente]');";
    $result = $mysqli_connection->query($raw_query);
    if ($result && $mysqli_connection->affected_rows > 0) {
      $response->getBody()->write(json_encode(array("message" => "created")));
      return $response->withHeader("Content-Type", "application/json")->withStatus(201);
    } else {
      $response->getBody()->write(json_encode(array("message" => $mysqli_connection->error)));
      return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }
  }

  public function update(Request $request, Response $response, $args)
  {
    $data = json_decode($request->getBody()->getContents(), true);
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if(($data["votazione"] > 100) || ($data["votazione"] < 0)) {
        $response->getBody()->write(json_encode(array("message" => "campi incorretti")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }
    $raw_query = "UPDATE certificazioni SET";
    $prima_aggiunta = true;
    if (isset($data["alunno_id"])) {
      if (!$prima_aggiunta) {
        $raw_query .= ",";
      }
      $raw_query .= " alunno_id='$data[alunno_id]'";
      $prima_aggiunta = false;
    }
    if (isset($data["titolo"])) {
      if (!$prima_aggiunta) {
        $raw_query .= ",";
      }
      $raw_query .= " titolo='$data[titolo]'";
      $prima_aggiunta = false;
    }
    if (isset($data["votazione"])) {
        if (!$prima_aggiunta) {
          $raw_query .= ",";
        }
        $raw_query .= " votazione='$data[votazione]'";
        $prima_aggiunta = false;
    }
    if (isset($data["ente"])) {
        if (!$prima_aggiunta) {
          $raw_query .= ",";
        }
        $raw_query .= " ente='$data[ente]'";
        $prima_aggiunta = false;
    }
    $raw_query .= " WHERE id=$args[id]";
    $result = $mysqli_connection->query($raw_query);
    if ($mysqli_connection->affected_rows > 0) {
        $response->getBody()->write(json_encode(array("message" => "updated")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    } else {
        $response->getBody()->write(json_encode(array("message" => "not found")));
        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
      }
  }

  public function destroy(Request $request, Response $response, $args)
  {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $raw_query = "DELETE FROM certificazioni WHERE id=$args[id]";
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