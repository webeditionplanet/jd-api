<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AuthToken;
use AppBundle\Form\Type\CredentialsType;
use AppBundle\Model\Credentials;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Class AuthTokenController
 */
class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $authUser = $em->getRepository('AppBundle:AuthUser')
            ->findOneBy([
                'login' => $credentials->getLogin(),
            ]);

        if (!$authUser) {
            return $this->invalidCredentials();
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($authUser, $credentials->getPassword());

        if (!$isPasswordValid) {
            return $this->invalidCredentials();
        }

        $authToken = $this->get('app_services_auth_token')->create($authUser);

        $em->persist($authToken);
        $em->flush();

        return $authToken;
    }

    /**
     * @throws BadCredentialsException
     */
    private function invalidCredentials()
    {
        throw new BadCredentialsException('Invalid credentials');
    }
}
