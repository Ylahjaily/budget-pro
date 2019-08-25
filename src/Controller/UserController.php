<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractFOSRestController
{
    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/api/users/{email}")
     */
    public function getApiUser(User $user){
        return $this->view($user);
    }

    /**
     * @Rest\Get("/api/users")
     *
     */
    public function getApiUsers(){
        $users = $this->userRepository->findAll();
        return $this->view($users);
    }

    /**
     * @Rest\Post("/api/users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function postApiUser(User $user,  ConstraintViolationListInterface $validationErrors)
    {
        $errors = array();
        if ($validationErrors->count() > 0)
        {
            /** @var ConstraintViolation $constraintViolation */
            foreach ($validationErrors as $constraintViolation ){
                // Returns the violation message. (Ex. This value should not be blank.)
                $message = $constraintViolation ->getMessage();
                // Returns the property path from the root element to the violation. (Ex. lastname)
                $propertyPath = $constraintViolation ->getPropertyPath();
                $errors[] = ['message' => $message, 'propertyPath' => $propertyPath];
            }
        }

        if (!empty($errors)) {
            // Throw a 400 Bad Request with all errors messages (Not readable, you can do better)
            throw new BadRequestHttpException(\json_encode( $errors));
        }

        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user);
    }

    /**
     * @Rest\Patch("/api/users/{email}")
     */
    public function patchApiUser(User $user, Request $request, ValidatorInterface $validator){

        $validationErrors = $validator->validate($user);
        if ($validationErrors->count() > 0) {
            // Same validator as POST   }
        }

        $attributes = [
            'firstname' => 'setFirstname',
            'lastname' => 'setLastname',
        ];
        foreach ( $attributes as $attribute => $setter){
            if ($request->get($attribute) === null){
                continue;
            }
            $user->$setter($request->get($attribute));
        }
        $this->em->flush();
        return $this->view($user);
    }

    /**
     * @Rest\Delete("/api/users/{email}")
     */
    public function deleteApiUser(User $user, Request $request){

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->view($user);

    }

    /**
     * @Rest\View(serializerGroups={"user"})
     */
    public function getUsers()
    {
        $users = $this->userRepository->findAll();
        return $this->view($users);
        // "get_users"
    }


}
