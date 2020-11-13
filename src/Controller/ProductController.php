<?php

namespace App\Controller;

use App\Entity\Product;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ProductController extends AbstractFOSRestController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(LoggerInterface $logger): Response
    {
        $logger->info('I just got the logger');

        $entityManager = $this->getDoctrine()->getManager();

        // $product = new Product();
        // $product->setName('Keyboard');
        // $product->setPrice(1999);

        // // tell Doctrine you want to (eventually) save the Product (no queries yet)
        // $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        // $entityManager->flush();


        return new Response("PES ETTİM! Saat sabahın 7'si oldu, saatlerdir bunun başındayım. Çok güzel ilerliyordum. Ama bu mysql işi bozdu");
    }
}
