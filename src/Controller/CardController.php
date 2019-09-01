<?php
namespace App\Controller;
use App\Entity\Card;
use App\Provider\CardProvider;
use App\Repository\CardRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class CardController extends AbstractFOSRestController
{
    private $cardRepository;
    private $em;

    public function __construct(CardRepository $cardRepository, EntityManagerInterface $em)
    {
        $this->cardRepository = $cardRepository;
        $this->em = $em;
    }
    /**
     * @Rest\Get("/api/admin/cards")
     */
    public function getApiAdminCards()
    {
        $cards = $this->cardRepository->findAll();
        return $this->view($cards);
    }
    /**
     * @Rest\Get("/api/admin/cards/{id}")
     * @Rest\View(serializerGroups={"adminCards"})

     */
    public function getApiAdminCard(Card $card)
    {
        return $this->view($card);
    }

    /**
     * @Rest\Delete("/api/admin/cards/{id}")
     */
    public function deleteApiAdminCard(Card $card)
    {
        $this->em->remove($card);
        $this->em->flush();
    }
}