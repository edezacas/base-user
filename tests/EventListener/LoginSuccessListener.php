<?php


namespace DigitalAscetic\BaseUserBundle\Tests\EventListener;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessListener implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $context = SerializationContext::create();
        $context->setGroups(['id', 'user.default'])->setSerializeNull(true);
        $jsonResponse = $this->serializer->serialize(['user' => $user], 'json', $context);
        return new Response($jsonResponse);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $jsonResponse = $this->serializer->serialize(['login_error' => $exception->getMessage()], 'json');
        return new Response($jsonResponse, 401);
    }
}