<?php

namespace RabotaBundle\Controller;

use RabotaBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use RabotaBundle\Entity\Product;

class DefaultController extends Controller
{

    public function getProductListAction(){

        $db = $this->getDoctrine()->getRepository(Product::class);
        $products = $db->findAll();

        return $this->render('RabotaBundle:Default:products.html.twig',['products' => $products]);
    }

    public function getOrderListAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $db = $this->getDoctrine()->getRepository(Order::class);

        $orders = $db->findBy(['user_id' => $user->getId() ]);
        return $this->render('RabotaBundle:Default:orders.html.twig',['orders' => $orders]);
    }

    public function newOrderAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $products = $this->getRequest()->request->all();
        $products = json_decode($products['cart_list']);

        if(count($products)>=1){
            $order = new Order();
            $em = $this->getDoctrine()->getManager();
            $total = 0;

            foreach ($products as $product){
                $db_product = $this->getDoctrine()->getRepository(Product::class)->find($product->product_id);
                $total += ($db_product->getPrice() * $product->product_quantity);
                $order->addProduct($db_product);
            }

            $order->setTotal($total);
            $order->setUserId($user->getId());

            $em->persist($order);
            $em->flush();
        }


        return new Response('order add');
    }

    public function newProductAction()
    {
        $request = $this->getRequest()->request->all();

        $product = new Product();
        $product->setName($request['product_name']);
        $product->setPrice(round($request['product_price']));
        $em = $this->getDoctrine()->getManager();

        $em->persist($product);
        $em->flush();

        return new Response();
    }

    public function delOrderAction(Order $order){

            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();


            //return $this->redirectToRoute('product_list');
            return new Response('order del');
    }

    public function delProductAction(Product $product){

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        //return $this->redirectToRoute('order_list');
        return new Response('product del');
    }

    public function editProductAction(Product $product){

        $inputs = $this->getRequest()->request->all();


        $em = $this->getDoctrine()->getManager();
        $product->setName($inputs['name']);
        $product->setPrice($inputs['price']);
        $em->persist($product);
        $em->flush();

        //return $this->redirectToRoute('order_list');
        return new Response('product update');

    }

    public function editOrderAction(Order $order){

        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute('order_list');
    }

    public function showOrderAction(Order $order){

        return $this->render('RabotaBundle:Default:showOrder.html.twig', array(
            'order' => $order,
            'products' => $order->getProducts(),
        ));
    }

}
