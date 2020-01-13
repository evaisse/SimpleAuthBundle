<?php
/**
 * User: evaisse
 * Date: 13/01/2020
 * Time: 20:31
 */

namespace evaisse\SimpleAuthBundle\Controller;

use evaisse\SimpleAuthBundle\Security\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SimpleAuthController
 * @package evaisse\SimpleAuthBundle\Controller
 */
class SimpleAuthController extends Controller
{

    /**
     * @Route("/auth/login", name="login")
     * @Template()
     *
     * @param Request             $request
     * @param AuthenticationUtils $authenticationUtils
     * @return array|Response
     */
    public function loginAction(Request $request)
    {
        dump($request->getMethod());

        if ($request->getMethod() === "POST") {

            $this->get('simple_auth')->loginWithRequest($request, new User("toto"));
            return $this->redirectToRoute("homepage");
        }

        return [
            'error'         => null,
        ];
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/auth/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $this->get('simple_auth')->logoutWithRequest($request);
        return $this->redirectToRoute('homepage');
    }
}