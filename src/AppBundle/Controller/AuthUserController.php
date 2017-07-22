<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AuthUser;
use AppBundle\Form\Type\AuthUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class AuthUserController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/users")
     */
    public function postUsersAction(Request $request)
    {
        $user = new AuthUser();
        $form = $this->createForm(AuthUserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();

            return $user;
        } else {
            return $form;
        }
    }
}
