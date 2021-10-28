<?php

namespace App\Gagan\Workflow\Controller;

use App\Gagan\Workflow\Document\Product;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\Registry;

class ProductController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private Registry            $registry,
        private DocumentManager     $dm,
    )
    {
    }

    /**
     * @Route("/api/admin/products"),methods={"GET"})
     */
    public function index(DocumentManager $dm):JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $products = $dm->createQueryBuilder(Product::class)
            ->hydrate(true);
        if (in_array('ROLE_SELLER', $user->getRoles())) {
            $products->field('userId')->equals($user->getId());
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $products->field('status')->equals('reviewing');
        }
        if (in_array('ROLE_SEO', $user->getRoles())) {
            $products->field('status')->equals('seo_reviewing');
        }
        $products = $products->getQuery()
            ->execute();
        return new JsonResponse($this->serializer->serialize($products, JsonEncoder::FORMAT), Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/admin/products/create", name="admin_products_create",methods={"POST"})
     */
    public function create(ValidatorInterface $validator, Request $request, DocumentManager $dm)
    {
//        dd($request->request->get('name'));
        $user = $this->get('security.token_storage')->getToken()->getUser();
//        $validator = Validation::createValidator();
//        $groups = new Assert\GroupSequence(['Default', 'custom']);
//        $constraint = new Assert\Collection([
//            'name' => new Assert\Required(),
//            'price'=>new Assert\Type('float'),
//        ]);
//        $violations = $validator->validate($request->request->all(), $constraint, $groups);
//        if (count($violations)) {
//            dd($violations);
//            $errors = $this->validator->validate($this->data);
//                throw new ValidatorException($this->createErrorMessage($violations), Response::HTTP_BAD_REQUEST,'');
//            return new JsonResponse(['message' => 'required parameter missing'], Response::HTTP_UNPROCESSABLE_ENTITY);
//        }
        $product = new Product();
        $product = $this->setProduct($product, $request);
        $product->setUserId($user->getId());
        $ticketWorkflow = $this->registry->get($product);
        $ticketWorkflow->apply($product, 'to_review');

        $dm->persist($product);
        $dm->flush();

        return new Response('Created product id ');
    }

    /**
     * @Route("/api/admin/products/transitions/{id}", name="admin_products_transitions",methods={"POST"})
     */
    public function transition(string $id, Request $request)
    {
        $product = $this->dm->find(Product::class, $id);
        if (!in_array($request->get('status'), ['ACCEPT', 'REJECT'])) {
            return new JsonResponse(['message' => 'Invalid status'], 422);
        }

        $ticketWorkflow = $this->registry->get($product);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $message = 'Product as been rejected';
        try {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                if ($request->get('status') == 'ACCEPT') {
                    $ticketWorkflow->apply($product, 'to_reviewed');
                    $message = 'Product as been forwarded for seo review';
                } else {
                    $ticketWorkflow->apply($product, 'reject');
                }

            }
            if (in_array('ROLE_SEO', $user->getRoles())) {
                if ($request->get('status') == 'ACCEPT') {
                    $ticketWorkflow->apply($product, 'publish');
                    $message = 'Product has been published';
                } else {
                    $ticketWorkflow->apply($product, 'seo_reject');
                }
            }


            $this->dm->persist($product);
            $this->dm->flush();
            return new  JsonResponse(['message' => $message], 201);
        } catch (NotEnabledTransitionException $e) {
            return new  JsonResponse(['message' => $e->getMessage()], 422);
        }


    }

    /**
     * @Route("/api/admin/products/{id}/update", name="admin_products_update",methods={"POST"})
     *
     */
    public function toReview(string $id, Request $request)
    {
        $product = $this->dm->find(Product::class, $id);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user->getId() !== $product->getUserId()) {
            return new JsonResponse(['message' => 'product doesnot belongs to you '], Response::HTTP_UNAUTHORIZED);
        }


        try {
            $product = $this->setProduct($product, $request);
            $ticketWorkflow = $this->registry->get($product);

            if ($product->getStatus() == 'draft') {
                $ticketWorkflow->apply($product, 'to_review');
            }

            $this->dm->persist($product);
            $this->dm->flush();
            return new  JsonResponse(['message' => 'product updated', 'data' => json_decode($this->serializer->serialize($product, JsonEncoder::FORMAT))], 201);
        } catch (Exception $e) {
            return new  JsonResponse(['message' => $e->getMessage()], 422);
        }


    }

    /**
     * @param $product
     * @param Request $request
     * @return mixed
     */
    private function setProduct($product, Request $request): Product
    {
        $product->setName($request->request->get('name'));
        $product->setPrice($request->request->get('price'));
        $product->setDescription($request->request->get('description'));
        $product->setSeoTitle($request->request->get('seoTitle'));
        $product->setSeoKeywords($request->request->get('seoKeywords'));
        $product->setStock($request->request->get('stock'));
        $product->setSeoDescription($request->request->get('seoDescription'));
        return $product;
    }
}
