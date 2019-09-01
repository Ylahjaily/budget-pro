<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

class SubscriptionController extends AbstractFOSRestController
{


    private $subscriptionRepository;
    private $em;

    static private $patchAttributes = [
        'name' => 'setName',
        'slogan' => 'setSlogan',
        'url' => 'setUrl',
    ];

    public function __construct(SubscriptionRepository $subscriptionRepository, EntityManagerInterface $em)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->em = $em;

    }


    /**
     * @Rest\Get("/api/subscriptions")
     */
    public function getApiSubscriptions()
    {
        $subscriptions = $this->subscriptionRepository->findAll();
        return $this->view($subscriptions);
    }

    /**
     * @Rest\Get("/api/subscriptions/{id}")
     * @Rest\View(serializerGroups={"subscriptions"})
     */
    public function getApiSubscription(Subscription $subscription)
    {
        return $this->view($subscription);
    }


    /**
     * @Rest\Get("/api/admin/subscriptions")
     */
    public function getApiAdminSubscriptions()
    {
        $subscriptions = $this->subscriptionRepository->findAll();
        return $this->view($subscriptions);
    }

    /**
     * @Rest\Get("/api/admin/subscriptions/{id}")
     */
    public function getApiAdminSubscription(Subscription $subscription)
    {
        return $this->view($subscription);
    }


    /**
     * @Rest\Post("/api/admin/subscriptions")
     * @ParamConverter("subscription", converter="fos_rest.request_body")
     */
    public function postApiAdminSubscriptions(Subscription $subscription, ConstraintViolationListInterface $validationErrors)
    {
        $errors = [];
        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }
        if(!empty($errors)) {
            return new JsonResponse($errors, 402);
        }
        $this->em->persist($subscription);
        $this->em->flush();
        return $this->view($subscription, 201);
    }


    /**
     * @Rest\Delete("/api/admin/subscriptions/{id}")
     */
    /**
     * @Rest\Patch("/api/admin/subscriptions/{id}")
     */
    public function patchApiAdminSubscription(Subscription $subscription, ValidatorInterface $validator, Request $request)
    {
        foreach(static::$patchAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $subscription->$setter($request->get($attribute));
        }
        $errors = [];
        $validationErrors = $validator->validate($subscription);
        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }
        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }
        $this->em->flush();
        return $this->view($subscription);
    }

    /**
     * @Rest\Delete("/api/admin/subscriptions/{id}")
     */
    public function deleteApiAdminSubscription(Subscription $subscription)
    {

        $this->em->remove($subscription);
        $this->em->flush();
    }

}
