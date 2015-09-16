<?php

namespace KinetiseSkeleton\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Comment
 * @package KinetiseSkeleton\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="comments")
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @JMS\Expose()
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="pub_date")
     * @JMS\Type("DateTime<'D, d M Y H:i:s O'>")
     * @JMS\Expose()
     */
    private $pubDate;

    /**
     * @return array
     * @JMS\XmlAttributeMap
     * @JMS\VirtualProperty
     */
    public function getItemAttributes()
    {
        return array(
            'k:context' => $this->id
        );
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->pubDate) {
            $this->pubDate = new \DateTime();
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Comment
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set pubDate
     *
     * @param \DateTime $pubDate
     *
     * @return Comment
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    /**
     * Get pubDate
     *
     * @return \DateTime
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }
}
