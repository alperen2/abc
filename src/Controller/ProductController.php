<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="get_all_products", methods={"GET"})
     */
    public function index(ProductRepository $product): Response
    {
        $data = $product->findAll();
        return $this->json($data, Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/product/{id}",  methods={"GET"})
     */
    public function get_one($id, ProductRepository $product, ValidatorInterface $validator): Response
    {
        $errors = $validator->validate($id, new Assert\Positive());

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $data = $product->find($id);

        if (!$data) {
            $message = [
                "message" => "No product found for id {$id}"
            ];
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }
        return $this->json($data, Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/product", name="add_product", methods={"POST"})
     */
    public function add(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): Response
    {

        $data = [
            "name" => $request->request->get("name"),
            "price" => (float) $request->request->get("price"),
        ];
        extract($data);


        $constraint = new Assert\Collection([
            'name' => new Assert\NotBlank(),
            'price' => new Assert\Positive(),
        ]);

        $errors = $validator->validate($data, $constraint);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product, Response::HTTP_OK);
    }

    /**
     * @Route("/product/{id}", name="update_product", methods={"PUT"})
     */
    public function update($id, EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): Response
    {

        $data = [
            "id" => (int) $id,
        ];

        $request->request->get("name") != Null  ? $data["name"] = $request->request->get("name") : false;
        $request->request->get("price") != Null ? $data["price"] = (float) $request->request->get("price") : false;
        extract($data);

        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            $message = [
                "message" => "No product found for id {$id}"
            ];
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $collection = [
            'name' => new Assert\NotBlank(),
            'price' => new Assert\Positive(),
            "id" =>  new Assert\Positive()
        ];

        $result = array_intersect_key($collection, $data);

        $constraint = new Assert\Collection($result);


        $errors = $validator->validate($data, $constraint);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        isset($name) ? $product->setName($name) : false;
        isset($price) ? $product->setPrice($price) : false;

        $entityManager->flush();


        return $this->json($product, Response::HTTP_OK);
    }

    /**
     * @Route("/product/{id}", name="delete_product", methods={"DELETE"})
     */
    public function delete($id, EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): Response
    {

        $data = [
            "id" => (int) $id,
        ];
        extract($data);

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            $message = [
                "message" => "No product found for id {$id}"
            ];
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($product);
        $entityManager->flush();


        return $this->json($product, Response::HTTP_OK);
    }
}
