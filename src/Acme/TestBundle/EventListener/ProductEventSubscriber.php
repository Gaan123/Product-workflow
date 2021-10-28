<?php

declare(strict_types=1);

namespace App\Acme\TestBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;

class ProductEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * BeerGlassEventSubscriber constructor.
     *
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;

    }

    public static function getSubscribedEvents(): array
    {

        return [
            'workflow.product_publishing.transition.to_review' => 'toReview',
            'workflow.product_publishing.transition.to_reviewed' => 'toReviewed',
            'workflow.product_publishing.transition.publish' => 'published',
            'workflow.product_publishing.transition.reject' => 'rejected',
            'workflow.product_publishing.transition.seo_reject' => 'seoRejected',
        ];
    }

    public function toReview(Event $event): void
    {
        $product = $event->getSubject();
        $product->setMessage('Your Product has been submitted to review');

    }
    public function toReviewed(Event $event): void
    {
        $product = $event->getSubject();
        $product->setMessage('Your Product has been submitted for seo reviews');

    }
    public function published(Event $event): void
    {
        $product = $event->getSubject();
        $product->setMessage('Your Product has been published');

    }
    public function rejected(Event $event): void
    {
        $product = $event->getSubject();
        $product->setMessage('update your product');

    }
     public function seoRejected(Event $event): void
    {
        $product = $event->getSubject();
        $product->setMessage('update your product seo info');

    }

}
