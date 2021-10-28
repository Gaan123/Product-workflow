<?php

namespace App\Acme\TestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @MongoDB\Document(collection="products")
 */
class Product
{
// the configured marking store property must be declared
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $name;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $status = 'draft';
    /**
     * @var string
     * @ODM\Field(type="object_id")
     */
    protected $userId;
    /**
     * @var float
     * @ODM\Field(type="float")
     */
    protected $price = 0;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $description = null;
    /**
     * @var string
     * @ODM\Field(type="int")
     */
    protected $stock = 0;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $message;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $seoTitle;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $seoDescription;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $seoKeywords;
    /**
     * @var date
     * @ODM\Field(type="date")
     */
    protected $createdAt;

    // getter/setter methods must exist for property access by the marking store
    public function getId()
    {
        return $this->id;
    }
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status, $context = [])
    {
        $this->status = $status;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    public function getMessage()
    {
        return $this->name;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;
    }

    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;
    }
}
