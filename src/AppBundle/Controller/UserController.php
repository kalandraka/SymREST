<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

class UserController extends Controller
{
    /**
     * @Rest\Get("/user")
     */
    public function getAction()
    {
        $restresult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if ($restresult === null) {
            $response = new Response();
            $response->setContent('There are no users exist.');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }
        return $this->json($restresult);
    }

    /**
     * @Rest\Get("/user/{id}")
     */
    public function idAction($id)
    {
        $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($singleresult === null) {
            $response = new Response();
            $response->setContent('User not found.');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }
        return $this->json($singleresult);
    }

    /**
     * @Rest\Post("/user/")
     */
    public function postAction(Request $request)
    {
        $data = new User;
        $data_ = $request->request->all();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        if (empty($data_['name']) || empty($data_['role']) || empty($data_['password'])) {
            $response->setContent('NULL VALUES ARE NOT ALLOWED');
            $response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            return $response;
        }
        $data->setName($data_['name']);
        $data->setPassword($data_['password']);
        $data->setRole($data_['role']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return $this->json($data);
    }

    /**
     * @Rest\Put("/user/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $data_ = $request->request->all();
        $sn = $this->getDoctrine()->getManager();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            $response->setContent('User not found.');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        } else {
            if (!empty($data_['name']))
                $user->setName($data_['name']);
            if (!empty($data_['password']))
                $user->setPassword($data_['password']);
            if (!empty($data_['role']))
                $user->setRole($data_['role']);
            $sn->flush();
            return $this->json($user);
        }
    }

    /**
     * @Rest\Delete("/user/{id}")
     */
    public function deleteAction($id)
    {
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        if (empty($user)) {
            $response->setContent('User not found.');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }
        else {
            $sn->remove($user);
            $sn->flush();
        }
        $response->setContent('User deleted successfully.');
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
