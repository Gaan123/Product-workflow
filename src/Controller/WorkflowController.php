<?php

declare(strict_types=1);

namespace App\Controller;

use App\Acme\TestBundle\Document\Product;
use App\Acme\TestBundle\Document\Ticket;
use App\Document\BeerGlass;
use App\Workflow\Transition\TicketTransitions;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Driver\Exception\LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class WorkflowController extends AbstractController
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * WorkflowController constructor.
     *
     * @param Registry $registry
     * @param DocumentManager $dm
     */
    public function __construct(Registry $registry, DocumentManager $dm)
    {
        $this->registry = $registry;
        $this->dm = $dm;
    }

    /**
     * @Route("/api/workflow", name="workflow", methods={"GET"})
     */
    public function index()
    {

        $workflows = [
            'beerglass_basic' => 'clean',
            'beerglass_2' => 'clean',
            'beerglass_complex' => 'clean',
            'complex_events' => 'clean',
        ];

        return new JsonResponse($workflows );
    }

    /**
     * @Route("/api/product", name="create_product", methods={"POST"})
     */
    public function createWorkflow(Request $request)
    {
        $productName = $request->request->get('name');
        $workflowStartingState = $request->request->get('state');
        $doc = new Product();
        $doc->setName($productName);

        $this->dm->persist($doc);
        $this->dm->flush();

        return new JsonResponse($doc,201);
    }

    /**
     * @param string $workflowName
     * @param string id
     * @Route("/workflow/{workflowName}/{id}", name="show_workflow", methods={"GET"})
     */
    public function workflow(string $workflowName, string $id)
    {
        $beerGlass = $this->dm->find(BeerGlass::class, $id);
        $workflow = $this->registry->get($beerGlass, $workflowName);

        return $this->render('workflow/show.html.twig', [
            'workflowName' => $workflowName,
            'workflow' => $workflow,
            'beerGlass' => $beerGlass,
        ]);
    }

    /**
     * @Route("/api/product/transition", methods={"POST"})
     */
    public function transition()
    {
        $ticket = new Product();
        $ticket->setName('Nice title');

        // Find workflow by entity
        // Will throw an exception if a same entity is targeted by multiple workflows
        // unless you provide second argument
        $ticketWorkflow = $this->registry->get($ticket);

        // Apply transition
        // Will try to play the transition (move from A place to B place) on the given entity
        $ticketWorkflow->apply($ticket, 'to_review');
//        $ticketWorkflow->apply($ticket, 'to_seo_review');
//        $ticketWorkflow->apply($ticket, 'publish');
//        $this->dm->persist($ticket);
//        $this->dm->flush();
        dd($ticket);
//        dd($ticket);
/*
        $workflow = $this->registry->get($beerGlass, 'product_publishing');
        $workflow->apply($beerGlass, 'to_review',[]);
        $this->dm->flush();

        return new JsonResponse($workflow);*/
    }
    /**
     * @Route("/api/trs", methods={"GET"})
     */
    public function trs(Registry $registry)
    {
        /*****************************************
         * Simple example
         */
        // Create new ticket
        $ticket = new Ticket();
        $ticket->setTitle('Nice title');

        // Find workflow by entity
        // Will throw an exception if a same entity is targeted by multiple workflows
        // unless you provide second argument
        $ticketWorkflow = $registry->get($ticket);

        // Apply transition
        // Will try to play the transition (move from A place to B place) on the given entity
        $ticketWorkflow->apply($ticket, TicketTransitions::START_PROCESS);
        $ticketWorkflow->apply($ticket, TicketTransitions::COMMENT);

        dd($ticket);
        return new Response("");
//        return $this->render('main.html.twig');
    }
}
