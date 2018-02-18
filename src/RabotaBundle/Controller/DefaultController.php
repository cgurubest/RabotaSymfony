<?php

namespace RabotaBundle\Controller;

use RabotaBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use RabotaBundle\Entity\Product;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('RabotaBundle:Default:products.html.twig');
    }

    public function rabotaAction()
    {
        return $this->render('RabotaBundle:Default:rabota.html.twig');
    }

    public function saveProductAction()
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $product->setName('Product_'.rand(0,100000));
        $product->setPrice('100');
        $em->persist($product);
        $em->flush();

        dump($product->getId());

        return $this->render('RabotaBundle:Default:rabota.html.twig');
    }


    public function addProductToOrderAction($order_id = 1 ){

        $em = $this->getDoctrine()->getManager();

        $order = $this->getDoctrine()->getRepository(Order::class)->find($order_id);
        $product = $this->getDoctrine()->getRepository(Product::class)->find(11);


        $order->addProduct($product);

        $em->persist($order);
        $em->persist($product);

        $em->flush();

        dump($order->getProducts());
        foreach ($order->getProducts() as $product){
            echo $product->getName();
        }

        dump($order);

        return $this->render('RabotaBundle:Default:rabota.html.twig');
    }

    public function saveOrderAction(){

        $em = $this->getDoctrine()->getManager();


        $product = new Product();
        $product->setPrice(100);
        $product->setName('name');



        $order = new Order();
        $order->setTotal('1000');
        $order->setUserId('1');
        $order->addProduct($product);


        $em->persist($order);
        $em->persist($product);

        $em->flush();


        dump($product);
        dump($order);



        return $this->render('RabotaBundle:Default:rabota.html.twig');
    }

    public function getProductListAction(){

        $db = $this->getDoctrine()->getRepository(Product::class);
        $products = $db->findAll();

        //dump($products);

        return $this->render('RabotaBundle:Default:products.html.twig',['products' => $products]);
    }

    public function newOrderAction()
    {
        $products = $this->getRequest()->request->all();

        if(count($products)>1){
            $order = new Order();
            $em = $this->getDoctrine()->getManager();
            $total = 0;

            foreach ($products as $id => $count){
                $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
                $total += ($product->getPrice() * $count);
                if($count>0){
                    $order->addProduct($product);
                }
            }

            $order->setTotal($total);
            $order->setUserId('1');

            $em->persist($order);
            $em->flush();
        }


        return new Response();
    }


    public function getOrderListAction(){

        $db = $this->getDoctrine()->getRepository(Order::class);
        $orders = $db->findAll();
        dump($orders);

        return $this->render('RabotaBundle:Default:rabota.html.twig');
    }
}
