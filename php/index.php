<?php
use Slim\Factory\AppFactory;


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/AlunniController.php';
require __DIR__ . '/controllers/CertificazioniController.php';

$app = AppFactory::create();

$app->get('/alunni', 'AlunniController:index');

$app->get('/alunni/{id}', 'AlunniController:alunniWithId');

//curl -X POST localhost:8080/alunni -H "Content-Type: application/json" -d "{ \"nome\":\"leoardo\", \"cognome\":\"jarro\" }"
$app->post('/alunni', 'AlunniController:create');

//curl -X PUT localhost:8080/alunni/1 -H "Content-Type: application/json" -d "{ \"nome\":\"leolino\" }"
$app->put('/alunni/{id}', 'AlunniController:update');

//curl -X DELETE localhost:8080/alunni/2
$app->delete('/alunni/{id}', 'AlunniController:destroy');

$app->get('/alunni/{id}/certificazioni', 'CertificazioniController:certificazioni_alunno');

$app->get('/certificazioni/{id}', 'CertificazioniController:certificazioneWithId');

//curl -X POST localhost:8080/alunni/11/certificazioni -H "Content-Type: application/json" -d "{ \"titolo\":\"gingillometria applicata\", \"votazione\":\"13\", \"ente\":\"unifi\" }"
$app->post('/alunni/{id}/certificazioni', 'CertificazioniController:create');

//curl -X PUT localhost:8080/certificazioni/4 -H "Content-Type: application/json" -d "{ \"titolo\":\"ingegneria della pasta\", \"votazione\":\"30\" }"
$app->put('/certificazioni/{id}', 'CertificazioniController:update');

//curl -X DELETE localhost:8080/certificazioni/45
$app->delete('/certificazioni/{id}', 'CertificazioniController:destroy');


$app->run();

?>