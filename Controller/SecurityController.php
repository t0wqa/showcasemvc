<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 8:02
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Exception\AuthenticationException;
use AppBundle\Exception\InvalidCredentialsException;
use AppBundle\Exception\InvalidDataException;
use AppBundle\Exception\InvalidTokenException;
use AppBundle\Exception\NoUserForCheckingException;
use AppBundle\Formatter\CodeCreatedFormatter;
use AppBundle\Formatter\CurrentUserFormatter;
use AppBundle\Formatter\UserCreatedFormatter;
use AppBundle\Formatter\ValidateSuccessFormatter;
use AppBundle\Security\UserActivator;
use AppBundle\Service\ApiResponse;
use PHPUnit\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class SecurityController extends Controller
{

    /**
     * @param Request $request
     * @param UserActivator $activator
     * @Route("/sendCode", methods={"GET"})
     * @return Response $response
     * @throws HttpException $exception
     */
    public function createCodeAction(Request $request, UserActivator $activator)
    {
        $activationData = $activator->createUserActivation($request->query->get('username'));

        $responseData = [
            'success' => true,
            'message' => $activationData['message'],
            'username' => $request->query->get('username')
        ];

        if ($this->container->get('kernel')->getEnvironment() == 'dev') {
            $responseData['activationCode'] = $activationData['activationCode'];
        }

        return ApiResponse::create(new CodeCreatedFormatter(), $responseData);
    }

    /**
     * @param Request $request
     * @Route("/activateUser", methods={"GET"})
     * @param UserActivator $activator
     * @return Response $response
     */
    public function activateUserAction(Request $request, UserActivator $activator)
    {
        $user = $activator->checkUserActivation($request->query->get('code'), $request->query->get('username'));

        $response = ApiResponse::create(new UserCreatedFormatter(), [
            'success' => true,
            'asr_authentication_token' => $user->getApiToken()
        ]);

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/currentUser", methods={"GET", "OPTIONS"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function currentUserAction(Request $request)
    {
        if ($request->getMethod() == 'OPTIONS') {
            $response = new JsonResponse();

            $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Cookie, Cookies, Auth-Token');

            return $response;
        }

        if (empty($request->headers->get('Auth-Token')) && empty($request->cookies->get('asr_authentication_token'))) {
            throw new InvalidTokenException();
        }

        $token = $request->headers->get('Auth-Token') ?? $request->cookies->get('asr_authentication_token');

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findByToken($token);

        if (!$user) {
            throw new InvalidTokenException();
        }

        return ApiResponse::create(new CurrentUserFormatter($request), $user);
    }

    /**
     * @param Request $request
     * @param EncoderFactoryInterface $encoderFactory
     * @Route("/login", methods={"POST", "OPTIONS"})
     * @return JsonResponse
     */
    public function logInAction(Request $request, EncoderFactoryInterface $encoderFactory)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return ApiResponse::create(new ValidateSuccessFormatter(), [
                'success' => true
            ]);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['password'])) {
            throw new InvalidDataException();
        }

        /**
         * @var User $user
         */
        if (
            !$user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $data['username']])
        ) {
            throw new InvalidCredentialsException();
        }

        $passwordEncoder = $encoderFactory->getEncoder($user);

        if (!$passwordEncoder->isPasswordValid($user->getPassword(), $data['password'], $user->getSalt())) {
            throw new InvalidCredentialsException();
        }

        $response = ApiResponse::create(new UserCreatedFormatter(), [
            'success' => true,
            'authentication_token' => $user->getApiToken()
        ]);

        return $response;
    }

    public function checkActivationLinkAction(Request $request)
    {

    }

}