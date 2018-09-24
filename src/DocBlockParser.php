<?php
namespace Packaged\DocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;

class DocBlockParser
{
  /**
   * @var DocBlock
   */
  protected $_docBlock;

  public function __construct($docBlock)
  {
    if($docBlock)
    {
      $this->_docBlock = DocBlockFactory::createInstance()->create(
        preg_replace(
          ['/^(\h*)(\/\*{2,})\h+(.+?)\h+(\*+\/)$/'],
          ["$1$2\n * $3\n $4"],
          $docBlock
        )
      );
    }
    else
    {
      $this->_docBlock = null;
    }
  }

  /**
   * @param $object
   *
   * @return DocBlockParser
   */
  public static function fromObject($object)
  {
    $reflect = new \ReflectionClass($object);
    return new self($reflect->getDocComment());
  }

  /**
   * @param $object
   * @param $method
   *
   * @return DocBlockParser
   */
  public static function fromMethod($object, $method)
  {
    $reflect = new \ReflectionMethod($object, $method);
    return new self($reflect->getDocComment());
  }

  /**
   * @param $object
   * @param $property
   *
   * @return DocBlockParser
   */
  public static function fromProperty($object, $property)
  {
    $reflect = new \ReflectionProperty($object, $property);
    return new self($reflect->getDocComment());
  }

  /**
   * @param      $object
   * @param null $filter
   *
   * @return DocBlockParser[]
   */
  public static function fromProperties($object, $filter = null)
  {
    $reflect = new \ReflectionClass($object);
    $parsers = [];
    foreach($reflect->getProperties($filter) as $property)
    {
      $parsers[$property->name] = new self($property->getDocComment());
    }
    return $parsers;
  }

  /**
   * Get the summary of the docblock
   *
   * @return null|string
   */
  public function getSummary()
  {
    return $this->_docBlock ? $this->_docBlock->getSummary() : '';
  }

  /**
   * Get the body of the docblock (excluding summary)
   *
   * @return null|string
   */
  public function getBody()
  {
    return $this->_docBlock ? (string)$this->_docBlock->getDescription() : '';
  }

  /**
   * Get the underlying docblock
   *
   * @return DocBlock
   */
  public function rawDocBlock()
  {
    return $this->_docBlock;
  }

  /**
   * Retrieve all the docblock tags
   *
   * @return array
   */
  public function getTags()
  {
    $return = [];
    foreach($this->_docBlock ? $this->_docBlock->getTags() : [] as $tag)
    {
      if(!isset($return[$tag->getName()]))
      {
        $return[$tag->getName()] = [];
      }

      $return[$tag->getName()][] = trim($tag->getDescription());
    }
    return $return;
  }

  /**
   * Check to see if the docblock has one or more tags by name
   *
   * @param $tag
   *
   * @return bool
   */
  public function hasTag($tag)
  {
    return $this->_docBlock ? $this->_docBlock->getTagsByName($tag) !== [] : false;
  }

  /**
   * Retrieve the number of instances of a tag
   *
   * @param $tag
   *
   * @return int
   */
  public function getTagCount($tag)
  {
    return $this->_docBlock ? count($this->_docBlock->getTagsByName($tag)) : 0;
  }

  /**
   * Get the value of a tag.  If one tag exists, the value will be returned.
   * If multiple items exist, the first value will be returned
   * If no items exist, the default value will be returned
   *
   * @param      $tag
   * @param null $default
   *
   * @return null|string
   */
  public function getTag($tag, $default = null)
  {
    $tags = $this->_docBlock ? $this->_docBlock->getTagsByName($tag) : [];

    if(empty($tags))
    {
      return $default;
    }

    return trim($tags[0]->getDescription());
  }

  /**
   * Retrieve the first tag with data, if none exist, return the default
   *
   * @param array $tags
   * @param null  $default
   *
   * @return null|string
   */
  public function getTagFailover(array $tags, $default = null)
  {
    foreach($tags as $tag)
    {
      $value = $this->getTag($tag);
      if($value !== null)
      {
        return $value;
      }
    }
    return $default;
  }
}
