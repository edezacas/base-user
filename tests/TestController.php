<?php


namespace DigitalAscetic\BaseUserBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{

    /**
     * @Route("/success", name="test.login.success", methods={"GET"})
     * @return JsonResponse
     */
    public function loginSuccess()
    {
        return new JsonResponse(['user' => $this->getUser()]);
    }

}