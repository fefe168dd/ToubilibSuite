<?php 
namespace toubilib\api\actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\ports\api\ServiceRdvInterface;
use toubilib\core\application\ports\api\dto\RdvDTO ;
use toubilib\core\application\ports\api\dto\InputRendezVousDTO;
use toubilib\core\application\exceptions\PraticienNotFoundException;
use toubilib\core\application\exceptions\PatientNotFoundException;
use toubilib\core\application\exceptions\InvalidMotifVisiteException;
use toubilib\core\application\exceptions\InvalideCreneauException;
use toubilib\core\application\exceptions\PraticienNotAvailableException;
use DateTime;
use Exception;

class AddRendezVous {

    private ServiceRdvInterface $service;

    public function __construct(ServiceRdvInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        // Récupérer les données du body de la requête POST
        $body = $request->getParsedBody();
        
        if (!isset($body['dateHeureDebut'], $body['dateHeureFin'], $body['praticienId'], $body['patientId'], $body['motifVisite'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid input. Required: dateHeureDebut, dateHeureFin, praticienId, patientId, motifVisite']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $dateHeureDebut = new DateTime($body['dateHeureDebut']);
            $dateHeureFin = new DateTime($body['dateHeureFin']);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Invalid date format']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $InputRendezVousDTO = new InputRendezVousDTO(
            $dateHeureDebut,
            $dateHeureFin,
            $body['praticienId'],
            $body['patientId'],
            $body['motifVisite']
        );
        
        try {
            $createdRdv = $this->service->creerRendezVous($InputRendezVousDTO);
            $payload = json_encode($createdRdv);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (PraticienNotFoundException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        } catch (PatientNotFoundException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        } catch (InvalidMotifVisiteException | InvalideCreneauException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        } catch (PraticienNotAvailableException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to create rendez-vous: ' . $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}