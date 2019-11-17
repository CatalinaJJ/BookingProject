<?php


namespace App\Controller;

use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Class APIController
 * @Route("/api")
 */
class APIController extends AbstractController
{
    private $entityManager;

    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }
    /**
     * Lists all available trips.
     * @Route("/trips", name="all_trips", methods={"GET"})
     */
    public function getAllTrips(Request $request) {

        $filter = $request->query->get('filter');
        $priceOrder = $request->query->get('price');
        $titleOrder = $request->query->get('title');
        $locationOrder = $request->query->get('location');
        $priceFrom = $request->query->get('priceFrom');
        $priceTo = $request->query->get('priceTo');
        $repository = $this->entityManager->getRepository(Trip::class);
        $trips = $repository->findAvailableTrips($filter, $priceOrder, $titleOrder, $locationOrder, $priceFrom, $priceTo);
        return new Response($this->serializer->serialize($trips, 'json'));
    }

    /**
     * List Trip information by slug.
     * @Route("/trip/{slug}", name="trip_page", methods={"GET"})
     */
    public function getTripBySlug($slug) {
        $trip = $this->getTripEntityBySlug($slug);
        return new Response($this->serializer->serialize($trip, 'json', ['ignored_attributes' => ['users']]));
    }

    /**
     * Book Trip.
     * @IsGranted("ROLE_USER")
     * @Route("/book/{slug}/{quantity}", name="book_trip")
     */
    public function bookTrip($slug, $quantity) {
        $user = $this->getUser();
        $trip = $this->getTripEntityBySlug($slug);

        if($trip) {
            $vacantSpaces = $trip->getVacantSpaces();
            if($quantity > 0 && is_numeric($quantity)) {
                if ($quantity > $vacantSpaces) {
                   return new Response("You cannot book more than available spaces.");
                }
                else {
                    // Trip exists and the quantity is validated.
                    $updated_trip = $this->updateTripSpaces($user, $trip, $vacantSpaces, $quantity);
                    return new Response($this->serializer->serialize($updated_trip, 'json', ['ignored_attributes' => ['users']]));
                }
            }
            else {
                return new Response("Invalid quantity inserted.");
            }
        }
        else {
            return new Response("No trip was found.");
        }

    }

    public function getTripEntityBySlug($slug) {
        $repository = $this->entityManager->getRepository(Trip::class);
        return $repository->findOneBy(['slug' => $slug]);
    }


    public function updateTripSpaces($user, $trip, $vacantSpaces, $quantity) {
        
        $trip->setVacantSpaces($vacantSpaces - $quantity);
        $trip->addUser($user);

        $user->addTrip($trip);
        $this->entityManager->persist($trip);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $trip;

    }




}