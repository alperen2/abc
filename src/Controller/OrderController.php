<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @Route("/api", name="api_")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order", methods={"GET"})
     */
    public function index(OrderRepository $order): Response
    {
        $data = $order->findAll();
        return $this->json($data);
    }

    /**
     * @Route("/order/{id}", name="get_order", methods={"GET"})
     */
    public function get_one($id, OrderRepository $order, ValidatorInterface $validator): Response
    {
        $errors = $validator->validate($id, new Assert\Positive());

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $data = $order->find($id);

        if (!$data) {
            $message = [
                "message" => "No product found for id {$id}"
            ];
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }
        return $this->json($data, Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/order", methods={"POST"}, name="add_order")
     */
    public function add(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, ProductRepository $product): Response
    {

        $data = [
            "product_id" => $request->request->get("product_id"),
            "quantity" => $request->request->get("quantity"),
            "address" => $request->request->get("address"),
            "shipping_date" => new \DateTime($request->request->get("shipping_date")),
        ];

        extract($data);

        $constraint = new Assert\Collection([
            "product_id" => [new Assert\Positive(), new Assert\Type("numeric")],
            "quantity" => [new Assert\Positive(), new Assert\Type("numeric")],
            "address" => new Assert\Type("string"),
            "shipping_date" => new Assert\NotBlank(),
        ]);

        $errors = $validator->validate($data, $constraint);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }


        $product2 = $product->find($product_id);


        $order = new Order();
        $order->setProduct($product2);
        $order->setQuantity($quantity);
        $order->setAddress($address);
        $order->setShippingDate($shipping_date);

        $entityManager->persist($order);


        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }


        $entityManager->flush();

        return $this->json($order, Response::HTTP_OK);
    }

    /**
     * @Route("/order/{id}", name="update_order", methods={"PUT"})
     */
    public function update($id, EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, ProductRepository $product): Response
    {


        $data = [
            "id" => (int) $id,
        ];

        $request->request->get("product_id") != Null  ? $data["product_id"] = $request->request->get("product_id") : false;
        $request->request->get("quantity") != Null ? $data["quantity"] = $request->request->get("quantity") : false;
        $request->request->get("address") != Null ? $data["address"] = $request->request->get("address") : false;
        $request->request->get("shipping_date") != Null ? $data["shipping_date"] = new \DateTime($request->request->get("shipping_date")) : false;
        extract($data);

        if ($shipping_date <= date("y-m-d")) {
            $message = [
                "message" => "Overdue orders cannot be updated",
            ];
            return $this->json($message, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $order = $entityManager->getRepository(Order::class)->find($id);
        if (!$order) {
            $message = [
                "message" => "No order found for id {$id}"
            ];
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $collection = [
            "id" => [new Assert\Positive(), new Assert\Type("numeric")],
            "product_id" => [new Assert\Positive(), new Assert\Type("numeric")],
            "quantity" => [new Assert\Positive(), new Assert\Type("numeric")],
            "address" => new Assert\Type("string"),
            "shipping_date" => new Assert\NotBlank(),
            // create a custom validator to validate the date
        ];

        $result = array_intersect_key($collection, $data);

        $constraint = new Assert\Collection($result);


        $errors = $validator->validate($data, $constraint);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $a = $product->find($product_id);

        isset($product_id) ? $order->setProduct($a) : false;
        isset($quantity) ? $order->setQuantity($quantity) : false;
        isset($address) ? $order->setAddress($address) : false;
        isset($shipping_date) ? $order->setShippingDate($shipping_date) : false;

        $entityManager->flush();


        return $this->json($order, Response::HTTP_OK);
    }
}
